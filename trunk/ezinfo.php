<?php
/**
 * eZInfo for JanrainAuth extension
 * @copyright Copyright (C) 2010 - SQLi Agency. All rights reserved
 * @licence http://www.gnu.org/licenses/gpl-2.0.txt GNU GPLv2
 * @author Jerome Vieilledent
 * @version @@@VERSION@@@
 * @package janrainauth
 */

class janrainauthInfo
{
    /**
     * eZInfo method
     */
    public static function info()
    {
        return array(
            'Name'            => 'Janrain Auth',
            'Version'         => '@@@VERSION@@@',
            'Copyright'       => 'Copyright © 2010 Jerome Vieilledent',
            'License'         => 'GNU General Public License v2.0',
            'Info'            => '<a href="http://www.janrain.com" target="_blank">Janrain Engage</a> eZ Publish implementation.<br />More info here : <a href="http://projects.ez.no/janrainauth" target="_blank">http://projects.ez.no/janrainauth</a>'
        );
    }
}
