<?php
declare(strict_types=1);

namespace Carica\Io\Amp {

  use Carica\Io\Event\Loop as EventLoop;
  use Carica\Io\Event\Loop\Listener as EventLoopListener;
  use Amp\Loop as AmpLoop;

  class LoopEvent implements EventLoopListener {

    /**
     * @var LoopAdapter
     */
    private $_loop;
    /**
     * @var string
     */
    private $_watcherId;

    /**
     * @param LoopAdapter $loop
     */
    public function __construct(LoopAdapter $loop, string $watcherId) {
      $this->_loop = $loop;
      $this->_watcherId = $watcherId;
    }

    /**
     * @return EventLoop|LoopAdapter
     */
    public function getLoop(): EventLoop {
      return $this->_loop;
    }

    public function remove(): void {
      AmpLoop::cancel($this->_watcherId);
    }
  }
}
