<?php
abstract class Mage_Task_TaskAbstract
{
    protected $_config = null;
    protected $_inRollback = false;
    protected $_stage = null;
    
    public abstract function getName();
    
    public abstract function run();
    
    public final function __construct(Mage_Config $config, $inRollback = false, $stage = null)
    {
        $this->_config = $config;
        $this->_inRollback = $inRollback;
        $this->_stage = $stage;
    }
    
    public function inRollback()
    {
        return $this->_inRollback;
    }
    
    public function getStage()
    {
        return $this->_stage;
    }
    
    public function getConfig()
    {
        return $this->_config;
    }
    
    public function getActionOption($name, $value = false)
    {
        return Mage_Console::getActionOption($name, $value);
    }
    
    public function init()
    {
    }
    
    protected final function _runLocalCommand($command, &$output = null)
    {
        return Mage_Console::executeCommand($command, $output);
    }
    
    protected final function _runRemoteCommand($command, &$output = null)
    {
        if ($this->_config->release('enabled', false) == true) {
            if ($this instanceOf Mage_Task_Releases_BuiltIn) {
                $releasesDirectory = '';

            } else {
                $releasesDirectory = '/'
                                   . $this->_config->release('directory', 'releases')
                                   . '/'
                                   . $this->_config->getReleaseId();
            }

        } else {
            $releasesDirectory = '';
        }
        
        $localCommand = 'ssh -p ' . $this->_config->getHostPort() . ' '
                      . '-q -o UserKnownHostsFile=/dev/null -o StrictHostKeyChecking=no '
                      . $this->_config->deployment('user') . '@' . $this->_config->getHostName() . ' '
                      . '"cd ' . rtrim($this->_config->deployment('to'), '/') . $releasesDirectory . ' && '
                      . $command . '"';

        return $this->_runLocalCommand($localCommand, $output);
    }
}