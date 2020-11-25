<?php

namespace WPML\PB\Elementor\Hooks;

use WPML\FP\Fns;
use WPML\FP\Logic;
use WPML\FP\Maybe;
use WPML\FP\Relation;
use WPML\LIB\WP\Post;
use WPML\PB\Elementor\DataConvert;
use WPML_Elementor_Data_Settings;
use function WPML\FP\curryN;
use function WPML\FP\pipe;

class GutenbergCleanup implements \IWPML_Backend_Action, \IWPML_Frontend_Action {

	public function add_hooks() {
		add_filter(
			'update_post_metadata',
			Fns::withoutRecursion( Fns::identity(), [ $this, 'removeGutenbergFootprint' ] ),
			10,
			4
		);
	}

	/**
	 * If we detect Gutenberg footprint in the Elementor data,
	 * we'll remove it and delete the Gutenberg string package.
	 *
	 * @param null|bool $check
	 * @param int       $postId
	 * @param string    $metaKey
	 * @param string    $metaValue
	 *
	 * @return mixed
	 */
	public function removeGutenbergFootprint( $check, $postId, $metaKey, $metaValue ) {
		if (
			WPML_Elementor_Data_Settings::META_KEY_DATA === $metaKey
			&& WPML_Elementor_Data_Settings::is_edited_with_elementor( $postId )
		) {
			// $ifValueHasChanged :: string -> bool
			$ifValueHasChanged = pipe( Relation::equals( $metaValue ), Logic::not() );

			// $update :: int -> string -> bool
			$update = curryN( 2, function( $postId, $meta ) {
				Post::updateMeta( $postId, WPML_Elementor_Data_Settings::META_KEY_DATA, $meta );
				self::deletePackage( self::getGutenbergPackage( $postId ) );
				return true;
			} );

			return Maybe::of( $metaValue )
				->map( [ DataConvert::class, 'unserialize' ] )
				->map( [ $this, 'removeBlockMetaInEditorWidget' ] )
				->map( [ DataConvert::class, 'serialize' ] )
				->filter( $ifValueHasChanged )
				->map( $update( $postId ) )
				->getOrElse( $check );
		}

		return $check;
	}

	/**
	 * @param array $data
	 *
	 * @return array
	 */
	public function removeBlockMetaInEditorWidget( array $data ) {
		foreach ( $data as &$element ) {
			if ( $element['elements'] ) {
				$element['elements'] = $this->removeBlockMetaInEditorWidget( $element['elements'] );
			} elseif ( 'widget' === $element['elType'] && isset( $element['settings']['editor'] ) ) {
				$element['settings']['editor'] = preg_replace( '(<!--[^<]*-->)', '', $element['settings']['editor'] );
			}
		}

		return $data;
	}

	/**
	 * @param int $postId
	 */
	public static function getGutenbergPackage( $postId ) {
		// $isGbPackage :: \WPML_Package -> bool
		$isGbPackage = Relation::propEq( 'kind_slug', 'gutenberg' );

		return wpml_collect( apply_filters( 'wpml_st_get_post_string_packages', [], $postId ) )
			->filter( $isGbPackage )
			->first();
	}

	/**
	 * @param \WPML_Package|null $package
	 */
	public static function deletePackage( $package ) {
		$package && do_action( 'wpml_delete_package', $package->name, $package->kind );
	}
}
