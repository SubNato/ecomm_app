<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\CourseType;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;

class CourseTypeController extends AdminController
{

    //This method is actually for showing tree form of the menus
    public function index(Content $content){
        $tree = new Tree(new CourseType);
        return $content->header('Course Types')
        ->body($tree);

    }
    //
    protected function detail($id)
    {
        $show = new Show(CourseType::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('title', __('Category'));
        $show->field('description', __('Description'));
        $show->field('order',__('Order'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
/** All of these are available on the laravel admin website. Check it out for more functions to use. */
        return $show;
    }

    //creating and editing.
    protected function form()
    {

        $form = new Form(new CourseType());

        $form->select('parent_id', __('Parent Category'))
        ->options((new CourseType())::selectOptions());  //This option does alot internally. You can check it out on their website (laravel-admin method).

        $form->text('title', __('Title'));//This text is similar to string in LARAVEL.
        $form->textarea('description', __('Description'));  //textarea is similar to text 
        $form->number('Order', __('Order'));  //number is similar to int.
        
        return $form;
    }
}
