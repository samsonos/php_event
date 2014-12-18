<?php
/**
 * Created by Vitaly Iegorov <egorov@samsonos.com>
 * on 04.09.14 at 18:05
 */
 namespace samson\core;

/**
 * Event managing system
 * @author Vitaly Egorov <egorov@samsonos.com>
 * @copyright 2014 SamsonOS
 */
class Event
{
    /** @var array Collection of registered events */
    protected static $listeners = array();

    /**
     * Return copy of event listeners
     * @return array Collection of all event listeners
     */
    public static function listeners()
    {
        return self::$listeners;
    }

    /**
     * Fire an event
     *
     * @param string $key    Event unique identifier
     * @param mixed  $params Event additional data
     * @param bool   $signal True if this event must be signaled only once
     *
     * @return mixed If signal is true then the event handler result will be returned,
     *               otherwise null
     */
    public static function fire($key, $params = array(), $signal = false)
    {
        // Convert to lowercase
        $key = strtolower($key);

        /** @var array $pointer Pointer to event handlers array */
        $pointer = & self::$listeners[$key];

        // If we have found listeners for this event
        if (isset($pointer)) {

            // Convert params to an array
            $params = is_array($params) ? $params : array(&$params);

            // If this is regular event firing
            if ($signal === false) {
                // Iterate all handlers
                foreach ($pointer as $handler) {
                    // Combine arguments
                    $args = array_merge($params, $handler[1]);

                    // Call external event handlers
                    call_user_func_array($handler[0], $args);
                }
            } else { // Call only first event subscriber as signal and return its result
                return call_user_func_array($pointer[0][0], array_merge($params, $pointer[0][1]));
            }
        }

        return null;
    }

    /**
     * Subscribe for event firing
     * @param string    $key        Event unique identifier
     * @param callback  $handler    Callback
     * @param array     $params     Additional callback parameters
     */
    public static function subscribe($key, $handler, $params = array())
    {
        // Convert to lowercase
        $key = strtolower($key);

        /** @var array $pointer Pointer to event handlers array */
        $pointer = & self::$listeners[$key];

        // Create event handlers array if not present
        $pointer = isset($pointer) ? $pointer : array();

        // Convert params to an array
        $params = is_array($params) ? $params : array(&$params);

        // Add event handler
        $pointer[] = array($handler, & $params);
    }
}
