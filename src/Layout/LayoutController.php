<?php


namespace CubexBase\Application\Layout;


use Cubex\Controller\Controller;
use Cubex\I18n\GetTranslatorTrait;
use CubexBase\Application\MainApplication;
use CubexBase\Application\Pages\PageClass;
use Generator;
use Packaged\Context\Context;
use Packaged\Http\Response;
use Packaged\I18n\TranslatableTrait;
use Packaged\Routing\Handler\Handler;
use Packaged\Routing\Route;
use Packaged\Ui\Element;
use Packaged\Ui\Html\HtmlElement;
use Psr\SimpleCache\CacheInterface;

use function is_array;
use function is_scalar;

/**
 * Class AbstractController
 * @package CubexBase\Application\Controller
 * @method \CubexBase\Application\Context\Context getContext() : Context
 */
abstract class LayoutController extends Controller
{

  use GetTranslatorTrait;
  use TranslatableTrait;

  /**
   * @return callable|Generator|Handler|Route[]|string
   */
  protected function _generateRoutes()
  {
    return '';
  }

  /**
   * @param Context $c
   * @param mixed $result
   * @param null $buffer
   * @return mixed|Response
   */
  protected function _prepareResponse(Context $c, $result, $buffer = null)
  {
    if (($result instanceof Element || $result instanceof HtmlElement || is_scalar($result) || is_array($result))) {
      $theme = new Layout();

      if ($result instanceof PageClass) {
        $theme->setPageClass($result->getPageClass());
      }

      $path = $c->request()->getRequestUri();
      $language = $c->request()->getPreferredLanguage();

      $theme->setContext($this->getContext())->setContent($result);

      if ($result->shouldCache()) {
        MainApplication::$_cache->set($path . $language, $theme->produceSafeHTML());
      }

      return parent::_prepareResponse($c, $theme, $buffer);
    }

    return parent::_prepareResponse($c, $result, $buffer);
  }

}