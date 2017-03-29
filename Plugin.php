<?php
if (!defined('__TYPECHO_ROOT_DIR__')) exit;

/**
 * Typecho评论微信通知插件
 *
 * @package CommentToWechat
 * @author lscho
 * @version 0.1
 * @link https://lscho.com/
 */
class CommentToWechat_Plugin implements Typecho_Plugin_Interface{
	/* 激活插件方法 */
	public static function activate(){
		//挂载评论接口
		Typecho_Plugin::factory('Widget_Feedback')->finishComment = array('CommentToWechat_Plugin', 'send');

		return '插件安装成功,请设置SCKEY';
	}
	 
	/* 禁用插件方法 */
	public static function deactivate(){
		return '插件禁用成功';
	}
	 
	/* 插件配置方法 */
	public static function config(Typecho_Widget_Helper_Form $form){
        $element = new Typecho_Widget_Helper_Form_Element_Text('sckey', null, '', 'SCKEY', '请填写 SCKEY');
        $form->addInput($element);		
	}

    /**
     * 个人用户的配置面板
     *
     * @access public
     * @param Typecho_Widget_Helper_Form $form
     * @return void
     */
    public static function personalConfig(Typecho_Widget_Helper_Form $form){
    	
    }	
	 
	/* 插件实现方法 */
	public static function render(){

	}

	/* 推送通知方法 */
	public static function send($post){
        //获取系统配置
        $options = Helper::options();
        //判断是否配置好API
        if (is_null($options->plugin('CommentToWechat')->sckey)) {
            throw new Typecho_Plugin_Exception(_t('SCKEY 未配置'));
        }
        $key=$options->plugin('CommentToWechat')->sckey;
		$postdata = http_build_query(
		    array(
		        'text' => '您的文章【'.$post->title.'】有新的评论',
		        'desp' => $post->text
		    )
		);
		$options = array('http' =>
		    array(
		        'method'  => 'POST',
		        'header'  => 'Content-type: application/x-www-form-urlencoded',
		        'content' => $postdata
		    )
		);
		$context  = stream_context_create($options);
		return $result = file_get_contents('http://sc.ftqq.com/'.$key.'.send', false, $context);		
	}
}