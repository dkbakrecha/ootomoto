<?php

return [
  'gcm' => [
      'priority' => 'normal',
      'dry_run' => false,
      'apiKey' => 'AIzaSyDQdN-FmZvrkJctAZoDzn_8jlC7JbIQQ8I',
  ],
  'fcm' => [
        'priority' => 'normal',
        'dry_run' => false,
        'apiKey' => 'AIzaSyDQdN-FmZvrkJctAZoDzn_8jlC7JbIQQ8I',
  ],
  'apn' => [
      'certificate' => public_path() . '/ios/flair_debug.pem',
      'passPhrase' => '', //Optional
      // 'passFile' => __DIR__ . '/iosCertificates/yourKey.pem', //Optional
      'dry_run' => true
  ]
];