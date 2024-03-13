<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Models\CourseType;
use App\Models\Course;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Layout\Content;
use Encore\Admin\Tree;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\DB;

class CourseController extends AdminController
{
    protected $title = 'Course';
    
    protected function grid(){
        $grid = new Grid(new Course());
        //The first argument is the database field. The second is how it appears in the backend as the column name in the table.
        $grid->column('id', __('Id'));

        //Authenticate Users
        if(Admin::user()->isRole('teacher')){
            $token = Admin::user()->token;
            $grid->model()->where('user_token', '=', $token);
        }

        $grid->column('user_token', __('Teacher'))->display(function ($token){//'::'- this means laravel eloquent.
            //For further processing of data, you can create any method inside it or do operation
            //return User::where('token', '=', $token)->value('name');      //Might not work anymore due to the code added above.
            $item = DB::table('admin_users')->where('token', '=', $token)->value('username');
            return $item;

        });
        $grid->column('recommended', __('Recommended'))->switch();
        $grid->column('name', __('Name'));
        //50, 50, refers to the image size displayed in the backend.
        $grid->column('thumbnail', __('Thumbnail'))->image('', 50, 50);

        $grid->column('description', __('Description'));
        $grid->column('type_id', __('Type id'));
        $grid->column('price', __('Price'));
        $grid->column('lesson_num', __('Lesson num'));
        $grid->column('video_length', __('Video length'));
        $grid->column('downloadable_resources', __('Resources num'));
        $grid->column('created_at', __('Created at'));
        

        return $grid;
    }
    
    /**
     * 
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Course::findOrFail($id));

        $show->field('id', __('Id'));
        
        $show->field('name', __('Name'));
        $show->field('thumbnail', __('Thumbnail'));
        
        $show->field('description', __('Description'));
        
        $show->field('price', __('Price'));
        $show->field('lesson_num', __('Lesson num'));
        $show->field('video_length', __('Video length'));
        $show->field('follow', __('Follow'));
        $show->field('score', __('Score'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));
/** All of these are available on the laravel admin website. Check it out for more functions to use. */
        return $show;
    }

    //creating and editing.
    protected function form()
    {
        //Remember these are the fields that are found in our database!!!!! 
        //They have to be identical to what was placed in the database!!!!!

        $form = new Form(new Course());
        $form->text('name', __('Name'));

        //To get our categories.
        //It is also a key value pair.
        //The last one listed is the key, which shows first(it shows the opposite when printed). It is a laravel method.
        $result = CourseType::pluck('title', 'id');
        //Select method helps you to select one of the options that comes from the result variable.
        $form->select('type_id', __('Category'))->options($result);
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();
        //File is used for video and other format like pdf/doc
        $form->file('video', __('Video'))->uniqueName();
        $form->text('description', __('Description'));
        //Decimal method helps with retrieving float format from the database.
        $form->decimal('price', __('Price'));
        $form->number('lesson_num', __('Lesson number'));
        $form->number('video_length', __('Video length'));
        $form->number('downloadable_resources', __('Resources num'));
        //For/to see who is posting.
        //$result = User::pluck('name', 'token');
        //$form->select('user_token', __('Teacher'))->options($result);      //Because teacher would be displayed twice.
        $form->display('created_at', __('Created at'));
        $form->display('updated_at', __('Updated at'));
        //Code for the Switch to set recommended or not.
        /*$states = [
            'on'  => ['value' => 1, 'text' => 'enable', 'color' => 'success'],
            'off' => ['value' => 0, 'text' => 'disable', 'color' => 'danger'],
        ];*/

        //Authenticate Users
        if(Admin::user()->isRole('teacher')){
            $token = Admin::user()->token;
            $userName = Admin::user()->username;
            $form->select('user_token', __('Teacher'))->options([$token=>$userName])->default($token)->readonly();
        }else{
            $res = DB::table('admin_users')->pluck('username', 'token');
            $form->select('user_token', __('Teacher'))->options($res);
        }
        
        $form->switch('recommended', __('Recommended'))->default(0);     //Sets a default value of 0.
        
        /*$form->text('title', __('Title'));//This text is similar to string in LARAVEL.
        $form->textarea('description', __('Description'));  //textarea is similar to text 
        $form->number('Order', __('Order'));  //number is similar to int.*/
        
        return $form;
    }
}
