<?php
/**
* BabiPHP : The Simple and Fast Development Framework (http://babiphp.org)
* Copyright (c) BabiPHP. (http://babiphp.org)
*
* Licensed under The GNU General Public License
* For full copyright and license information, please see the LICENSE.txt
* Redistributions of files must retain the above copyright notice.
*
* @copyright     Copyright (c) BabiPHP. (http://babiphp.org)
* @link          http://babiphp.org BabiPHP Project
* @since         BabiPHP v 0.8.9
* @license       http://www.gnu.org/licenses/ GNU License
*
* 
* Not edit this file
*
*/

namespace BabiPHP\Component\View;

use ArrayAccess;
use BadMethodCallException;
use BabiPHP\Component\View\Engines\EngineInterface;

class View implements ArrayAccess
{
    /**
     * The view factory instance.
     *
     * @var \BabiPHP\Component\View\Factory
     */
    protected $factory;

    /**
     * The engine implementation.
     *
     * @var \BabiPHP\Component\View\Engines\EngineInterface
     */
    protected $engine;

    /**
     * The name of the view.
     *
     * @var string
     */
    protected $view;

    /**
     * The array of view data.
     *
     * @var array
     */
    protected $data;

    /**
     * The path to the view file.
     *
     * @var string
     */
    protected $path;

    /**
     * Create a new view instance.
     *
     * @param  \BabiPHP\Component\View\Factory  $factory
     * @param  \BabiPHP\Component\View\Engines\EngineInterface  $engine
     * @param  string  $view
     * @param  string  $path
     * @param  array   $data
     * @return void
     */
    public function __construct(Factory $factory, EngineInterface $engine, $view, $path, $data = [])
    {
        $this->view = $view;
        $this->path = $path;
        $this->engine = $engine;
        $this->factory = $factory;

        $this->data = (array) $data;
    }

    /**
     * Get the string contents of the view.
     *
     * @param  callable|null  $callback
     * @return string
     */
    public function render(callable $callback = null)
    {
        $contents = $this->renderContents();

        $response = isset($callback) ? call_user_func($callback, $this, $contents) : null;

        // Once we have the contents of the view, we will flush the sections if we are
        // done rendering all views so that there is nothing left hanging over when
        // another view gets rendered in the future by the application developer.
        $this->factory->flushSectionsIfDoneRendering();

        return !is_null($response) ? $response : $contents;
    }

    /**
     * Get the contents of the view instance.
     *
     * @return string
     */
    protected function renderContents()
    {
        // We will keep track of the amount of views being rendered so we can flush
        // the section after the complete rendering operation is done. This will
        // clear out the sections for any separate views that may be rendered.
        $this->factory->incrementRender();

        $contents = $this->getContents();

        // Once we've finished rendering the view, we'll decrement the render count
        // so that each sections get flushed out next time a view is created and
        // no old sections are staying around in the memory of an environment.
        $this->factory->decrementRender();

        return $contents;
    }

    /**
     * Get the sections of the rendered view.
     *
     * @return array
     */
    public function renderSections()
    {
        return $this->render(function () {
            return $this->factory->getSections();
        });
    }

    /**
     * Get the evaluated contents of the view.
     *
     * @return string
     */
    protected function getContents()
    {
        return $this->engine->get($this->path, $this->gatherData());
    }

    /**
     * Get the data bound to the view instance.
     *
     * @return array
     */
    protected function gatherData()
    {
        $data = array_merge($this->factory->getShared(), $this->data);

        foreach ($data as $key => $value) {
            if ($value instanceof Renderable) {
                $data[$key] = $value->render();
            }
        }

        return $data;
    }

    /**
     * Add a piece of data to the view.
     *
     * @param  string|array  $key
     * @param  mixed   $value
     * @return $this
     */
    public function with($key, $value = null)
    {
        if (is_array($key)) {
            $this->data = array_merge($this->data, $key);
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Add a view instance to the view data.
     *
     * @param  string  $key
     * @param  string  $view
     * @param  array   $data
     * @return $this
     */
    public function nest($key, $view, array $data = [])
    {
        return $this->with($key, $this->factory->make($view, $data));
    }

    /**
     * Get the view factory instance.
     *
     * @return \BabiPHP\Component\View\Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Get the view's rendering engine.
     *
     * @return \BabiPHP\Component\View\Engines\EngineInterface
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function name()
    {
        return $this->getName();
    }

    /**
     * Get the name of the view.
     *
     * @return string
     */
    public function getName()
    {
        return $this->view;
    }

    /**
     * Get the array of view data.
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get the path to the view file.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set the path to the view.
     *
     * @param  string  $path
     * @return void
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Determine if a piece of data is bound.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get a piece of bound data to the view.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->data[$key];
    }

    /**
     * Set a piece of data on the view.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->with($key, $value);
    }

    /**
     * Unset a piece of data from the view.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Get a piece of data from the view.
     *
     * @param  string  $key
     * @return mixed
     */
    public function &__get($key)
    {
        return $this->data[$key];
    }

    /**
     * Set a piece of data on the view.
     *
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public function __set($key, $value)
    {
        $this->with($key, $value);
    }

    /**
     * Check if a piece of data is bound to the view.
     *
     * @param  string  $key
     * @return bool
     */
    public function __isset($key)
    {
        return isset($this->data[$key]);
    }

    /**
     * Remove a piece of bound data from the view.
     *
     * @param  string  $key
     * @return bool
     */
    public function __unset($key)
    {
        unset($this->data[$key]);
    }

    /**
     * Dynamically bind parameters to the view.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return \BabiPHP\Component\View\View
     *
     * @throws \BadMethodCallException
     */
    public function __call($method, $parameters)
    {
        if (strpos($method, 'with') === 0) {
            $value = substr($method, 4);

            if (!ctype_lower($value)) {
                $value = preg_replace('/\s+/', '', $value);
                $value = strtolower(preg_replace('/(.)(?=[A-Z])/', '$1'.'_', $value));
            }

            return $this->with($value, $parameters[0]);
        }

        throw new BadMethodCallException("Method [$method] does not exist on view.");
    }

    /**
     * Get the string contents of the view.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }
}
