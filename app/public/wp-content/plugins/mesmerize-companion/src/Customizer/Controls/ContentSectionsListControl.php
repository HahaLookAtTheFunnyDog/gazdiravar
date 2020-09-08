<?php

namespace Mesmerize\Customizer\Controls;

class ContentSectionsListControl extends RowsListControl
{
    private static $enqueued = false;
    
    public function init()
    {
        $this->cpData['type']      = 'mod_changer';
        $this->type                = $this->cpData['type'];
        $this->cpData['selection'] = apply_filters('cloudpress\customizer\control\content_sections\multiple', 'check');
        parent::init();
    }
    
    
    public function enqueue()
    {
        
        if (static::$enqueued) {
            return;
        }
        
        static::$enqueued = true;
        $jsUrl            = get_template_directory_uri() . "/customizer/js/";
//        wp_enqueue_script('mesmerize-row-list-control', $jsUrl . "/row-list-control.js", array('jquery', 'customize-base'), null, true);
        wp_localize_script($this->companion()->getThemeSlug() . '-row-list-control', 'mesmerize_content_list_control_l10n', array(
            'in_pro'   => esc_html__('Available in PRO', 'mesmerize'),
            'insert'   => esc_html($this->cpData['insertText']),
            'in_page'  => esc_html__('Section is already in page', 'mesmerize'),
            'pro_only' => esc_html__('Pro Only', 'mesmerize'),
        ));
    }
    
    public function renderModChanger()
    {
        $items = $this->getSourceData();
        
        if (isset($items['use_ajax']) && $items['use_ajax'] && isset($items['filter'])) {
            
            ?>
            <ul <?php $this->dataAttrs(); ?> class="list rows-list" data-ajax-data="<?php echo esc_attr($items['filter']); ?>">

            </ul>
            <script type="text/template" <?php $this->dataAttrs(); ?> id="tmpl-row-template-<?php echo esc_attr($this->id); ?>">
                <li title="{{ data.id }}" class="item available-item {{ data.proOnly }}" data-varname="{{ data.optionsVar }}" data-id="{{ data.id }}">
                    <div class="image-holder" style="background-position:center center;">
                        <img src="{{ data.thumb }}"/>
                    </div>

                    <# if(data.proOnly) { #>
                    <span data-id="{{ data.id }}" data-pro-only="true" class="available-item-hover-button" data-setting-link="{{ data.setting }}">
                        <# print(mesmerize_content_list_control_l10n.in_pro) #>
                    </span>
                    <# } else { #>
                    <span data-id="{{ data.id }}" class="available-item-hover-button" data-setting-link="{{ data.setting }}">
                        <# print(mesmerize_content_list_control_l10n.insert) #>
                    </span>
                    <# } #>

                    <div title="{{ mesmerize_content_list_control_l10n.in_page }}" class="checked-icon"></div>
                    <div title="{{ mesmerize_content_list_control_l10n.pro_only }}" class="pro-icon"></div>
                    <span class="item-preview" data-preview="{{ data.preview }}"><i class="icon"></i></span>
                    <# if(data.description) { #>
                    <span class="description"><# print(data.description) #></span>
                    <# } #>
                </li>
            </script>

            <script type="text/template" <?php $this->dataAttrs(); ?> id="tmpl-row-category-template-<?php echo esc_attr($this->id); ?>">
                <li data-category="{{ data.category }}" class="category-title">
                    <span>{{ data.label }}</span>
                </li>
            </script>
            <?php
            return;
        } ?>

        <ul <?php $this->dataAttrs(); ?> class="list rows-list">
            <?php foreach ($items as $category => $data): ?>
                
                <?php $label = apply_filters('cloudpress\customizer\control\content_sections\category_label', $category, $category); ?>

                <li data-category="<?php echo $category ?>" class="category-title">
                    <span><?php echo $label; ?></span>
                </li>
                
                <?php foreach ($data as $item): ?>
                    <?php $used = ($item['id'] === $this->value()) ? "already-in-page" : ""; ?>
                    <?php $proOnly = isset($item['pro-only']) ? "pro-only" : ""; ?>

                    <li title="<?php echo $item['id']; ?>" class="item available-item <?php echo $used; ?> <?php echo $proOnly; ?>" data-id="<?php echo $item['id']; ?>">
                        <div class="image-holder" style="background-position:center center;">
                            <img data-src="<?php echo $item['thumb']; ?>" src=""/>
                        </div>
                        
                        <?php if ($proOnly) : ?>
                            <span data-id="<?php echo $item['id']; ?>" data-pro-only="true" class="available-item-hover-button" <?php $this->getSettingAttr(); ?> >
                                <?php _e('Available in PRO', 'cloudpress-companion') ?>
                            </span>
                        <?php else: ?>
                            <span data-id="<?php echo $item['id']; ?>" class="available-item-hover-button" <?php $this->getSettingAttr(); ?> >
                                <?php echo $this->cpData['insertText']; ?>
                            </span>
                        <?php endif; ?>

                        <div title="Section is already in page" class="checked-icon"></div>
                        <div title="Pro Only" class="pro-icon"></div>
                        <span class="item-preview" data-preview="<?php echo $item['preview']; ?>">
                            <i class="icon"></i>
                        </span>
                        <?php if (isset($item['description'])): ?>
                            <span class="description"> <?php echo $item['description']; ?> </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>

        <input type="hidden" value="<?php echo esc_attr(json_encode($this->value())); ?>" <?php $this->link(); ?> />
        
        <?php
        ;
    }
}
