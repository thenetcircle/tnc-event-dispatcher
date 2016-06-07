<?php
// quick fix for running in symfony.
if(@class_exists('sfConfig'))
    require_once sfConfig::get('sf_plugins_dir') . '/tncCorePlugin/lib/component.php';


/**
 * TncLog
 *
 * @package    tncEventDispatcherPlugin
 * @subpackage config
 *
 * @author     The NetCircle
 *
 * @method static TncEventDispatcher_Manager manager(string $type)
 */
class TncEventDispatcher extends TncCore_Component
{
    // OVERRIDES/IMPLEMENTS
    protected function initialize(TncCore_Environment $environment_)
    {
        // Include a file here (or where preferred) to override the defaults above
        if(TncCore_Environment::isSymfonyApplication()) {
            // Read community membership settings.
            /*include sfContext::getInstance()->getConfigCache()->checkConfig(
                'config/plugins/tncEventDispatcherPlugin/settings.yml'
            );*/

            // $this->categories = sfConfig::get('plugin_settings_tncLogPlugin_categories');
        }

        $this->service('manager', 'TncEventDispatcher_Factory');
        //--------------------------------------------------------------------------
    }
}

// register component
TncEventDispatcher::add();
//----------------------------------------------------------------------------
