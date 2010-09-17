<?php
/**
 * Janrain module fetch function definition
 * @copyright Copyright (C) 2010 - Jerome Vieilledent. All rights reserved
 * @licence http://www.gnu.org/licenses/gpl-2.0.txt GNU GPLv2
 * @author Jerome Vieilledent
 * @version @@@VERSION@@@
 * @package janrainauth
 */

$FunctionList = array();

$FunctionList['token_url'] = array( 'name' => 'token_url',
                                    'operation_types' => 'read',
                                    'call_method' => array( 'class' => 'JanrainAuthFunctionCollection',
                                                            'method' => 'getTokenURL' ),
                                    'parameter_type' => 'standard',
                                    'parameters' => array() );

$FunctionList['signin_url'] = array( 'name' => 'signin_url',
                                    'operation_types' => 'read',
                                    'call_method' => array( 'class' => 'JanrainAuthFunctionCollection',
                                                            'method' => 'getSignInURL' ),
                                    'parameter_type' => 'standard',
                                    'parameters' => array() );
