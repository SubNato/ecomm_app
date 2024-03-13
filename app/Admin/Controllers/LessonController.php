<?php

namespace App\Admin\Controllers;

use App\Models\Lesson;
use App\Models\Course;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;
use Encore\Admin\Facades\Admin;
use Illuminate\Support\Facades\DB;

class LessonController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'Lesson';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Lesson());

        //Authenticate Users
        if(Admin::user()->isRole('teacher')){
            $token = Admin::user()->token;
            $ids = DB::table('courses')->where('user_token', '=', $token)->pluck('id')->toArray();
            $grid->model()->whereIn('course_id', $ids);
        }

        $grid->column('id', __('Id'));
        $grid->column('course_id', __('Course id'));
        $grid->column('name', __('Name'));
        $grid->column('thumbnail', __('Thumbnail'))->image(50, 50);
        $grid->column('description', __('Description'));
        //$grid->column('video', __('Video'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Lesson::findOrFail($id));

        $show->field('id', __('ID'));
        $show->field('name', __('Name'));
        
        $show->field('course_id', __('Course name'));
        
        $show->field('thumbnail', __('Thumbnail'));
        $show->field('description', __('Description'));
        $show->field('video', __('Video'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Lesson());

        //$result = Course::pluck('name', 'id');     //Querying the database.
        $form->text('name', __('Name'));

        //Authenticate Users
        if(Admin::user()->isRole('teacher')){
            $token = Admin::user()->token;
            $ids = DB::table('courses')->where('user_token', '=', $token)->pluck('name', 'id');
            $form->select('course_id', __('Courses'))->options($ids);
        }else{
            $res = DB::table('courses')->pluck('name', 'id');
            $form->select('course_id', __('Courses'))->options($res);
        }

        //$form->select('course_id', __('Courses'))->options($result);
        $form->image('thumbnail', __('Thumbnail'))->uniqueName();        
        $form->textarea('description', __('Description'));
        
        if($form->isEditing()){

            //access this during form editing
            //  dump($form->video);

            $form->table('video', function($form){
            $form->text('name');
            $form->hidden('old_url');
            $form->hidden('old_thumbnail');
            $form->image('thumbnail')->uniqueName();
            //The file refers to any form of media.
            $form->file('url');
            });
        }else{
            //normal form submission or form creation.
            //$form->text('video', __('Video'));
            $form->table('video', function($form){
            $form->text('name')->rules('required');
            $form->image('thumbnail')->uniqueName()->rules('required');
            //The file refers to any form of media.
            $form->file('url')->rules('required');
            });
        }
        //saving call back function gets called before submitting to the database
        //but after clicking the submit button.
        //a good place to process grabbed data or form data.
        $form->saving(function (Form $form){
            if($form->isEditing()){
                    //Here is the place to process data.
                //The one below gets the editted data
                $video = $form->video;
                //The one below gets data from the database
                $res = $form->model()->video;
                //For each of the key ($k), get the value ($v).

                $path = env('APP_URL') . "uploads/";

                $newVideo = [];
                foreach($video as $k=>$v){
                    $oldVideo = [];
                    //User did not type anything.
                    if(empty($v['url'])){
                        $oldVideo["old_url"] = empty($res[$k]['url'])?""
                        //Replacing the domain path from the value
                        :str_replace($path, "", $res[$k]['url']);     //You have to check if it is empty, because you can't save null as a value in json in the database. so IF it is EMPTY, you save an empty string in it.
                    }else{                                                           //This is mainly because you are working with JSON Data.
                        //this is a new edited value.
                        $oldVideo["url"] = $v['url'];
                    }

                    if(empty($v['thumbnail'])){
                        $oldVideo["old_thumbnail"] = empty($res[$k]['thumbnail'])?""
                        //Replacing the domain path from the value
                        :str_replace($path, "", $res[$k]['thumbnail']);     //You have to check if it is empty, because you can't save null as a value in json in the database. so IF it is EMPTY, you save an empty string in it.
                    }else{                                                           //This is mainly because you are working with JSON Data.
                        //this is a new edited value.
                        $oldVideo["thumbnail"] = $v['thumbnail'];
                    }

                    if(empty($v['name'])){
                        $oldVideo["name"] = empty($res[$k]['name'])?""
                        
                        :$res[$k]['name'];     //Name is always the same.
                    }else{
                        //this is a new edited value.
                        $oldVideo["name"] = $v['name'];
                    }

                    $oldVideo['_remove_'] = $v['_remove_'];
                    array_push($newVideo, $oldVideo);
                }
            $form->video = $newVideo;
            
            }

            //dump($form->model()->video);
            //dump($form->video);  //Not using 'dd();'here because it would stop after the first output. 'dump();' allows to print multiple outputs with readable lines one after another for better debugging!
        });

        return $form;
    }
}
