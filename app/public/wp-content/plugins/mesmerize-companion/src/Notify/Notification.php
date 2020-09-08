<?php


namespace Mesmerize\Notify;


use DateTime;

class Notification
{
    const NOTIFICATION_ACTION_PREFIX = "cp_notification_notice_";
    
    private $name;
    
    private $start = '*';
    private $end = '*';
    private $after = null;
    
    private $dismissible = true;
    private $type = "info";
    private $active_callback = null;
    private $handle = null;
    private $priority = 0;
    
    private $data;
    
    public function __construct($data)
    {
        $this->data = $data;
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                
                if ($key === 'after') {
                    if (intval($value)) {
                        $this->$key = intval($value);
                    }
                } else {
                    $this->$key = $value;
                }
                
                
            }
        }
        
        if ($this->canShow()) {
            
            $this->addNotificationView();
        }
    }
    
    // php 5.3 compatibility
    public function __get($name)
    {
        if (property_exists($this, $name)) {
            return $this->$name;
        } else {
            throw new \Exception("Property {$name} does not exists in class Notification", 1);
            
        }
    }
    
    public function addNotificationView()
    {
        $self = $this;
        add_action('admin_notices', function () use ($self) {
            ?>
            <div data-cp-notification-name="<?php echo $self->name ?>" class="cp-notification notice notice-<?php echo $self->type ?> <?php echo($self->dismissible ? 'is-dismissible' : '') ?>">
                
                
                <?php
                if ($self->handle) {
                    call_user_func($self->handle, $self->data);
                } else {
                    do_action(\Mesmerize\Notify\Notification::NOTIFICATION_ACTION_PREFIX . $self->name, $self->data);
                }
                ?>
                
                <?php if ($self->dismissible): ?>
                    <script type="text/javascript">
                        jQuery('[data-cp-notification-name="<?php echo $self->name ?>"]').on('click', '.notice-dismiss', function () {
                            var data = {
                                'action': 'cp_dismiss_notification',
                                'notification': '<?php echo $self->name; ?>'
                            };
                            jQuery.post(ajaxurl, data).done(function (response) {

                            });
                        })
                    </script>
                <?php endif; ?>
            </div>
            <?php
        }, 0);
    }
    
    public function canShow()
    {
        $canShow = (
            $this->isActive() &&
            ! $this->isDismissed() &&
            $this->inTimeBoundaries()
        );
        
        return $canShow;
    }
    
    public function isActive()
    {
        if ( ! $this->active_callback) {
            return true;
        } else {
            return call_user_func($this->active_callback);
        }
    }
    
    public function inTimeBoundaries()
    {
        
        $time = new DateTime("now");
        
        if ($this->after) {
            $installTime = intval(NotificationsManager::initializationTS());
            $showAfter   = strtotime('+' . $this->after . ' days', $installTime);
            if ($showAfter <= $time->getTimeStamp()) {
                return true;
            }
        } else {
            
            if ($this->start === "*") {
                return true;
            } else {
                $start = \DateTime::createFromFormat('d-m-Y', $this->start);
                if ($start && $start <= $time) {
                    if ($this->end === "*") {
                        return true;
                    } else {
                        $end = \DateTime::createFromFormat('d-m-Y', $this->end);
                        if ($end && $time <= $end) {
                            return true;
                        }
                    }
                }
            }
        }
        
        return false;
    }
    
    public function isDismissed()
    {
        if ( ! $this->dismissible) {
            return false;
        }
        
        $notifications = get_option(NotificationsManager::DISMISSED_NOTIFICATIONS_OPTION, array());
        
        return in_array($this->name, $notifications);
    }
}
