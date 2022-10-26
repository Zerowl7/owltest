<?php

namespace App\Http\Admin;

use AdminColumn;
use AdminColumnFilter;
use AdminDisplay;
use AdminForm;
use AdminFormElement;
use Illuminate\Database\Eloquent\Model;
use SleepingOwl\Admin\Contracts\Display\DisplayInterface;
use SleepingOwl\Admin\Contracts\Form\FormInterface;
use SleepingOwl\Admin\Contracts\Initializable;
use SleepingOwl\Admin\Form\Buttons\Cancel;
use SleepingOwl\Admin\Form\Buttons\Save;
use SleepingOwl\Admin\Form\Buttons\SaveAndClose;
use SleepingOwl\Admin\Form\Buttons\SaveAndCreate;
use SleepingOwl\Admin\Section;
use App\Models\Product as Products;

/**
 * Class Orderitems
 *
 * @property \App\Models\Orderitem $model
 *
 * @see https://sleepingowladmin.ru/#/ru/model_configuration_section
 */
class Orderitems extends Section implements Initializable
{
    /**
     * @var bool
     */
    protected $checkAccess = false;

    /**
     * @var string
     */
    protected $title;

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


            AdminColumn::link('product.name', 'Name', 'created_at')->setWidth('250px')
            
                ->setOrderable(function($query, $direction) {
                    $query->orderBy('created_at', $direction);
                })
            ,
            AdminColumn::text('price', 'стоимость')->setWidth('80px')->setHtmlAttribute('class', 'text-center'),
            AdminColumn::text('discount', 'скидка')->setWidth('100px')->setHtmlAttribute('class', 'text-center'),
            AdminColumn::text('count', 'кол-во')->setWidth('100px')->setHtmlAttribute('class', 'text-center'),
            AdminColumn::text('cost', 'сумма')->setWidth('100px')->setHtmlAttribute('class', 'text-center'),

        ];

        $display = AdminDisplay::datatables()
            ->with('product','order')
            ->setName('firstdatatables')
            ->setOrder([[0, 'asc']])
            ->setDisplaySearch(true)
            ->paginate(25)
            ->setColumns($columns)
            ->setHtmlAttribute('class', 'table-primary table-hover th-center')
        ;

        if (isset($payload['order_id'])) {
            $display->setApply(function ($query) use (&$payload) {
                $query->where('order_id', $payload['order_id']);
            });
        }

        return $display;
    }

    public function is_session_exists() {
        $sessionName = session_name();
        if (isset($_COOKIE[$sessionName]) || isset($_REQUEST[$sessionName])) {
            session_start();
            return !empty($_SESSION);
        }
        return false;
    }


    /**
     * @param int|null $id
     * @param array $payload
     *
     * @return FormInterface
     */
    public function onEdit($id = null, $payload = [])
    {
        $tabs = AdminDisplay::tabbed();
        $tabs->setTabs(function ($tab) {
            $tabs = [];
        

        //////
        $form = AdminForm::card()->addBody([
            AdminFormElement::columns()->addColumn([
                
                AdminFormElement::hidden('order_id')->setDefaultValue($_SESSION['order_id']), 
                AdminFormElement::hidden('product_id'), 

                AdminFormElement::html('<h6 >Наименование товара: <span class="formname"> </span><h6>'),

                AdminFormElement::html('<h6>Цена :<span class="formprice"> </span> <h6>'),

                AdminFormElement::hidden('price'),                     
                     AdminFormElement::text('count', 'Количество')->required(),
                     AdminFormElement::select('proc', 'скидка %', ['0','5', '10', '15'])->setDefaultValue(0)
                     ->setValueSkipped(true),
                     AdminFormElement::hidden('discount', 'скидка'),
                     AdminFormElement::html('<h6>Сумма :<span class="formcost"> </span> <h6>'),
                     AdminFormElement::hidden('cost')->required(),

                
            ], 'col-xs-12 col-sm-6 col-md-4 col-lg-4')->addColumn([
                AdminFormElement::text('id', 'ID')->setReadonly(true),

                
            ], 'col-xs-12 col-sm-6 col-md-8 col-lg-8'),
        ]);

        $form->getButtons()->setButtons([
            'save'  => new Save(),
            'save_and_close'  => new SaveAndClose(),
            // 'save_and_create'  => new SaveAndCreate(),
            'cancel'  => (new Cancel()),
        ]);

        $tabs[] = AdminDisplay::tab($form)->setLabel('Orders')->setHtmlAttribute('class','orders');

        $html =  AdminFormElement::columns()->addColumn([
            AdminSection::getModel(Products::class)
                    ->fireDisplay()
                    ->addScript('custom-script', asset('js/orderitems.js'))
            ], 'col-md-12 productsorderitems');

            $tabs[] = AdminDisplay::tab($html)->setLabel('Товары')->setHtmlAttribute('class','tovar');




        return $tabs;
    });
    
    return $tabs;

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
