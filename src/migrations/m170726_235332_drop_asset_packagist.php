<?php

namespace craft\migrations;

use Composer\Json\JsonFile;
use Composer\Json\JsonManipulator;
use Craft;
use craft\db\Migration;

/**
 * m170726_235332_drop_asset_packagist migration.
 */
class m170726_235332_drop_asset_packagist extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        // See if we can find composer.json
        $jsonPath = defined('CRAFT_COMPOSER_PATH') ? CRAFT_COMPOSER_PATH : CRAFT_BASE_PATH.'/composer.json';
        if (!file_exists($jsonPath)) {
            Craft::warning('Could not remove the asset-packagist.org repo from composer.json because composer.json could not be found.', __METHOD__);
            return true;
        }

        // Get the Composer config
        $json = new JsonFile($jsonPath);
        $config = $json->read();

        // Remove the asset-packagist repo, if it's in there
        foreach ($config['repositories'] as $key => $repo) {
            if (isset($repo['url']) && strpos($repo['url'], '//asset-packagist.org') !== false) {
                unset($config['repositories'][$key]);
                // Reset the keys if numeric
                if (is_numeric($key)) {
                    $config['repositories'] = array_merge($config['repositories']);
                }
                $json->write($config);
                break;
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m170726_235332_drop_asset_packagist cannot be reverted.\n";
        return false;
    }
}