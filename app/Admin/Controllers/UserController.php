<?php

namespace App\Admin\Controllers;

use App\Models\User;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class UserController extends AdminController
{
    protected $title ='Members'; /** This changes the name of the table in the laravel backend server */
    //The grid() method is just to show rows.
    protected function grid()
    {
        $grid = new Grid(new User());

        $grid->column('id', __('Id'));
        $grid->column('name', __('Name'));
        $grid->column('email', __('Email'));
        
        $grid->column('created_at', __('Created at'));
        $grid->disableActions();     /** This is used to take off the edit, add, and delete option in the backend of your app. */

        $grid->disableCreateButton();
        $grid->disableExport();
        $grid->disableFilter();
/** All of these are available on the laravel admin website. Check it out for more functions to use. */
        return $grid;
    }
//Just for view (the detail method).
    protected function detail($id)
    {
        $show = new Show(User::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('name', __('Name'));
        $show->field('email', __('Email'));
        
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }
//It get's called when you create a new form or edit a row or info.
    protected function form()
    {
        $form = new Form(new User());

        $form->textarea('name', __('Name'));
        $form->textarea('email', __('Email'));
        

        return $form;
    }
}