<?php
/**
 * Customise the JSON responses
 */

namespace App\Providers;

/**
 * @uses
 */
use Illuminate\Support\ServiceProvider;
use Illuminate\Routing\ResponseFactory;

/**
 * Class JsonServiceProvider
 *
 * @package App\Providers
 */
class JsonServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application
     *
     * @param ResponseFactory $factory
     *
     * @return void
     */
    public function boot(ResponseFactory $factory)
    {
        /*
         * Handle success responses
         */
        $factory->macro('success', function( $message = '', $data = null ) use ($factory) {
           $format = [
               'status' => 'ok',
               'success' => true,
               'message' => $message,
               'data' => $data
           ];
           return $factory->make($format);
        });

        /*
         * Handle errors
         */
        $factory->macro('error', function( $message = '', $errors = [] ) use ($factory) {
            $format = [
                'status' => 'ok',
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ];
            return $factory->make($format);
        });
    }

    /**
     * Register the application
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
