<?php

namespace App\Http\Admin;

use AdminColumn;
use AdminColumnFilter;
use AdminDisplay;
use AdminForm;
use AdminFormElement;
use AdminSection;
use App\Models\OrderItem;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Form\Buttons\Cancel;
use SleepingOwl\Admin\Form\Buttons\Save;
use SleepingOwl\Admin\Form\Buttons\SaveAndClose;
use SleepingOwl\Admin\Form\Buttons\SaveAndCreate;
use SleepingOwl\Admin\Section;

/**
 * Class Orders
 *
 * @property \App\Models\Order $model
 *
 * @see https://sleepingowladmin.ru/#/ru/model_configuration_section
 */
class Orders extends Section implements Initializable
{
    /**
     * @var bool
     */
    protected $checkAccess = false;

    /**
     * @var string
     */
    protected $title = 'Заказы';

    /**
     * @var string
     */
    protected $alias;

    /**
     * Initialize class.
     */
    public function initialize()
    {
        $this->addToNavigation()->setPriority(100)->setIcon('fa fa-lightbulb-o');
    }

    /**
     * @param array $payload
     *
     * @return DisplayInterface
     */
    public function onDisplay($payload = [])
    {
        $columns = [
            AdminColumn::text('id', '#')->setWidth('50px')->setHtmlAttribute('class', 'text-center'),

            AdminColumn::link('customer', 'Customer', 'created_at')
                ->setSearchCallback(function ($column, $query, $search) {
                    return $query;
                }),

            AdminColumn::text('phone', 'телефон')->setWidth('150px')->setHtmlAttribute('class', 'text-center'),

            AdminColumn::text('type', 'тип')->setHtmlAttribute('class', 'text-center'),

            AdminColumn::text('status', 'Статус')->setWidth('150px')->setHtmlAttribute('class', 'text-left'),

            AdminColumn::text('user.name', 'Менеджер')->setWidth('150px')->setHtmlAttribute('class', 'text-bold text-left'),

            AdminColumn::text('created_at', 'Created / updated', 'updated_at')
                ->setWidth('160px')
                ->setOrderable(function ($query, $direction) {
                    $query->orderBy('updated_at', $direction);
                })
                ->setSearchable(false),
        ];

        $display = AdminDisplay::datatables()
            ->setName('firstdatatables')
            ->setOrder([[0, 'asc']])
            ->setDisplaySearch(true)
            ->paginate(25)
            ->setColumns($columns)
            ->setHtmlAttribute('class', 'table-primary table-hover th-center');

        $display->setColumnFilters([
            AdminColumnFilter::select()
                ->setModelForOptions(\App\Models\Order::class, 'customer')

                ->setLoadOptionsQueryPreparer(function ($element, $query) {
                    return $query;
                })
                ->setDisplay('customer')
                ->setColumnName('customer')
                ->setPlaceholder('All customers'),
        ]);
        $display->getColumnFilters()->setPlacement('card.heading');

        return $display;
    }

    /**
     * @param int|null $id
     * @param array $payload
     *
     * @return FormInterface
     */
    public function onEdit($id = null, $payload = [])
    {
        //Find current id

        $tabs = AdminDisplay::tabbed();

        $tabs->setTabs(function ($tab) use (&$id) {
            $tabs = [];

            $form = AdminForm::card()->addBody([
                AdminFormElement::columns()->addColumn([

                    AdminFormElement::html('<h5>Заказ № ' . $id . '</h5>'),
                    
                    AdminFormElement::hidden('id'),
                    
                    
                    AdminFormElement::text('customer', 'Покупатель')
                        ->required(),
                    
                        AdminFormElement::text('phone', 'Телефон')
                        ->required(),
                    
                        AdminFormElement::select('type', 'Тип')->setEnum(['online', 'offline'])->setDisplay('type'),
                    
                        AdminFormElement::select('status', 'Статус')->setEnum(['Active', 'Completed', 'Canceled'])->setDisplay('status'),

                        AdminFormElement::select('user_id', 'Менеджер', User::class)
                        ->setDisplay('name'),
                        
                        AdminFormElement::html('<hr>'),
                    AdminFormElement::datetime('created_at')
                        ->setVisible(true)
                        ->setReadonly(false),

                ], 'col-xs-12 col-sm-6 col-md-4 col-lg-4')->addColumn([

                ], 'col-xs-12 col-sm-6 col-md-8 col-lg-8'),
            ]);

            $form->getButtons()->setButtons([
                'save'  => new Save(),
                'save_and_close'  => new SaveAndClose(),
                'save_and_create'  => new SaveAndCreate(),
                'cancel'  => (new Cancel()),
            ]);

        
            $tabs[] = AdminDisplay::tab($form)->setLabel('SEO');
             if (!is_null($id)) {
                 $this->is_session_exists();
                 $_SESSION['order_id'] = $id;

                $table = AdminSection::getModel(OrderItem::class)->fireDisplay(['order_id' => $id]);//
                $tabs[] = AdminDisplay::tab($table)->setLabel('Данные');
             }

            return  $tabs;
        });


        return $tabs;
    }


    public function is_session_exists()
    {
        $sessionName = session_name();
        if (isset($_COOKIE[$sessionName]) || isset($_REQUEST[$sessionName])) {
            session_start();
            return !empty($_SESSION);
        }
        return false;
    }



    /**
     * @return FormInterface
     */
    public function onCreate($payload = [])
    {
        return $this->onEdit(null, $payload);
    }

    /**
     * @return bool
     */
    public function isDeletable(Model $model)
    {
        return true;
    }

    /**
     * @return void
     */
    public function onRestore($id)
    {
        // remove if unused
    }
}
