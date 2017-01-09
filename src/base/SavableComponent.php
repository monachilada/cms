<?php
/**
 * @link      https://craftcms.com/
 * @copyright Copyright (c) Pixel & Tonic, Inc.
 * @license   https://craftcms.com/license
 */

namespace craft\base;

use craft\events\ModelEvent;

/**
 * SavableComponent is the base class for classes representing savable Craft components in terms of objects.
 *
 * @property bool  $isNew    Whether the component is new (unsaved)
 * @property array $settings The component’s settings
 *
 * @author Pixel & Tonic, Inc. <support@pixelandtonic.com>
 * @since  3.0
 */
abstract class SavableComponent extends Component implements SavableComponentInterface
{
    // Traits
    // =========================================================================

    use SavableComponentTrait;

    // Constants
    // =========================================================================

    /**
     * @event ModelEvent The event that is triggered before the component is saved
     *
     * You may set [[ModelEvent::isValid]] to `false` to prevent the component from getting saved.
     */
    const EVENT_BEFORE_SAVE = 'beforeSave';

    /**
     * @event ModelEvent The event that is triggered after the component is saved
     */
    const EVENT_AFTER_SAVE = 'afterSave';

    /**
     * @event ModelEvent The event that is triggered before the component is deleted
     *
     * You may set [[ModelEvent::isValid]] to `false` to prevent the component from getting deleted.
     */
    const EVENT_BEFORE_DELETE = 'beforeDelete';

    /**
     * @event \yii\base\Event The event that is triggered after the component is deleted
     */
    const EVENT_AFTER_DELETE = 'afterDelete';

    // Static
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function isSelectable()
    {
        return true;
    }

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function getIsNew()
    {
        return (!$this->id || strpos($this->id, 'new') === 0);
    }

    /**
     * @inheritdoc
     */
    public function getSettings()
    {
        $settings = [];

        foreach ($this->settingsAttributes() as $attribute) {
            $settings[$attribute] = $this->$attribute;
        }

        return $settings;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function settingsAttributes()
    {
        $class = new \ReflectionClass($this);
        $names = [];

        foreach ($class->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            if (!$property->isStatic() && $property->getDeclaringClass()->getName() === static::class) {
                $names[] = $property->getName();
            }
        }

        return $names;
    }

    // Events
    // -------------------------------------------------------------------------

    /**
     * @inheritdoc
     */
    public function beforeSave($isNew)
    {
        // Trigger a 'beforeSave' event
        $event = new ModelEvent([
            'isNew' => $isNew,
        ]);
        $this->trigger(self::EVENT_BEFORE_SAVE, $event);

        return $event->isValid;
    }

    /**
     * @inheritdoc
     */
    public function afterSave($isNew)
    {
        // Trigger an 'afterSave' event
        $this->trigger(self::EVENT_AFTER_SAVE, new ModelEvent([
            'isNew' => $isNew,
        ]));
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        // Trigger a 'beforeDelete' event
        $event = new ModelEvent();
        $this->trigger(self::EVENT_BEFORE_DELETE, $event);

        return $event->isValid;
    }

    /**
     * @inheritdoc
     */
    public function afterDelete()
    {
        // Trigger an 'afterDelete' event
        $this->trigger(self::EVENT_AFTER_DELETE);
    }
}