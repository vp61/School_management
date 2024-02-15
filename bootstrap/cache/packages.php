<?php return array (
  'consoletvs/charts' => 
  array (
    'providers' => 
    array (
      0 => 'ConsoleTVs\\Charts\\ChartsServiceProvider',
    ),
  ),
  'fideloper/proxy' => 
  array (
    'providers' => 
    array (
      0 => 'Fideloper\\Proxy\\TrustedProxyServiceProvider',
    ),
  ),
  'intervention/image' => 
  array (
    'providers' => 
    array (
      0 => 'Intervention\\Image\\ImageServiceProvider',
    ),
    'aliases' => 
    array (
      'Image' => 'Intervention\\Image\\Facades\\Image',
    ),
  ),
  'knox/pesapal' => 
  array (
    'providers' => 
    array (
      0 => 'Knox\\Pesapal\\PesapalServiceProvider',
    ),
    'aliases' => 
    array (
      'Pesapal' => 'Knox\\Pesapal\\Facades\\Pesapal',
    ),
  ),
  'laravel/tinker' => 
  array (
    'providers' => 
    array (
      0 => 'Laravel\\Tinker\\TinkerServiceProvider',
    ),
  ),
  'laravelcollective/html' => 
  array (
    'providers' => 
    array (
      0 => 'Collective\\Html\\HtmlServiceProvider',
    ),
    'aliases' => 
    array (
      'Form' => 'Collective\\Html\\FormFacade',
      'Html' => 'Collective\\Html\\HtmlFacade',
    ),
  ),
  'tzsk/payu' => 
  array (
    'providers' => 
    array (
      0 => 'Tzsk\\Payu\\Provider\\PayuServiceProvider',
    ),
    'aliases' => 
    array (
      'Payment' => 'Tzsk\\Payu\\Facade\\Payment',
    ),
  ),
);