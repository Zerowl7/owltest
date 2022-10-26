<?php

namespace App\Providers;

use SleepingOwl\Admin\Providers\AdminSectionsServiceProvider as ServiceProvider;
use SleepingOwl\Admin\Contracts\Navigation\NavigationInterface;

class AdminSectionsServiceProvider extends ServiceProvider
{

    /**
     * @var array
     */
    protected $sections = [
        \App\Models\User::class => 'App\Http\Admin\Users',
        \App\Models\Product::class => 'App\Http\Admin\Product',
        \App\Models\Order::class => 'App\Http\Admin\Orders',
         \App\Models\OrderItem::class => 'App\Http\Admin\Orderitems',
    ];

    /**
     * Register sections.
     *
     * @param \SleepingOwl\Admin\Admin $admin
     * @return void
     */
    public function boot(\SleepingOwl\Admin\Admin $admin)
    {
    	//
        $this->app->call([$this, 'registerNavigation']);
        parent::boot($admin);
    }

    /**
     * @param NavigationInterface $navigation
     */
    public function registerNavigation( NavigationInterface $navigation ) {
        require base_path( 'app/admin/navigation.php' );
         $navigation->setFromArray(
                  require base_path( 'app/admin/navigation.php' )
      );
      }
}
