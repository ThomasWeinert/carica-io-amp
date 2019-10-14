<?php
declare(strict_types=1);

namespace Carica\Io\Amp {

  use Carica\Io;
  use Carica\Io\Deferred\Promise;
  use Carica\Io\Event\Loop as EventLoop;
  use Carica\Io\Event\Loop\Listener as EventListener;
  use Exception;
  use Amp\Loop as AmpLoop;

  class LoopAdapter implements EventLoop {

    /**
     * @return EventLoop
     */
    public static function create(): EventLoop {
      return new self();
    }

    /**
     * @return EventLoop|self
     */
    public static function get(): EventLoop {
      return EventLoop\Factory::get(
        static function() { return self::create(); }
      );
    }

    /**
     * @param callable $callback
     * @param int $milliseconds
     * @return EventListener
     */
    public function setTimeout(callable $callback, int $milliseconds): EventListener {
      return new LoopEvent($this, AmpLoop::delay($milliseconds, $callback));
    }

    /**
     * @param callable $callback
     * @param int $milliseconds
     * @return EventListener
     */
    public function setInterval(callable $callback, int $milliseconds): EventListener {
      return new LoopEvent($this, AmpLoop::repeat($milliseconds, $callback));
    }

    /**
     * @param callable $callback
     * @param resource $stream
     * @return EventListener
     * @throws Exception
     */
    public function setStreamReader(callable $callback, $stream): EventListener {
      return new LoopEvent(
        $this,
        AmpLoop::onReadable(
          $stream,
          static function($watcherId, $stream) use ($callback) {
            if (!is_resource($stream) || @feof($stream)) {
              AmpLoop::cancel($watcherId);
            } else {
              $callback($stream);
            }
          }
        )
      );
    }

    /**
     * @param EventListener $listener
     */
    public function remove(EventListener $listener): void {
      $listener->remove();
    }

    /**
     * @param Promise|NULL $for
     */
    public function run(Promise $for = NULL): void {
      if (NULL !== $for) {
        if ($for->state() !== Io\Deferred::STATE_PENDING) {
          return;
        }
        $for->always(
          static function () { AmpLoop::stop(); }
        );
      }
      AmpLoop::run();
    }

    public function stop(): void {
      AmpLoop::stop();
    }
  }
}
