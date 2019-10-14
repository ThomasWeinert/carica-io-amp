<?php
require __DIR__.'/../vendor/autoload.php';

$loop = Carica\Io\Amp\LoopAdapter::get();

$loop->setInterval(
  static function() {
    static $i = 0;
    echo 'C', ++$i, ' ';
  },
  2000
);

\Amp\Loop::repeat(
  3000,
  static function() {
    static $i = 0;
    echo 'R', ++$i, ' ';
  }
);

$loop->run();
