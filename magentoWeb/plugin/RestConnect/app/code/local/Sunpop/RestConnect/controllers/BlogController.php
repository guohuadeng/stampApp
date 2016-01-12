<?php
/**
 * * NOTICE OF LICENSE
 * * This source file is subject to the Open Software License (OSL 3.0)
 *
 * Author: Ivan Deng
 * QQ: 300883
 * Email: 300883@qq.com
 * @copyright  Copyright (c) 2008-2015 Sunpop Ltd. (http://www.sunpop.cn)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Sunpop_RestConnect_BlogController extends Mage_Core_Controller_Front_Action {
	
	public function getCategoryListAction(){
		
		$categories = Mage::getModel("blog/cat")->getCollection();
		$result = array();
		foreach($categories as $category){
			$result[] = $category->getData();
		}
		echo json_encode ($result);
	}
	/* 
	* @Param int category_id
	* return json
	*/
	public function getCategoryPostListAction(){
		
		$collection = Mage::getModel('blog/blog')->getCollection()
                ->addPresentFilter()
                ->addEnableFilter(Smartwave_Blog_Model_Status::STATUS_ENABLED)
                ->addStoreFilter()
                ->joinComments();
		$category_id = $this->getRequest()->getParam('category_id');
		if($category_id){
			$category = Mage::getModel("blog/cat")->load($category_id);
			if(!$category->getId()){
				echo json_encode ( array (
						'status' => '0x0003',
						'message' => 'category not exists'
					));
				return ;
			}else{
				$collection->addCatFilter($category->getCatId());
			}
		}
		
		$result = array();
		if(count($collection)>0){
			foreach($collection as $c){
				$result[] = $c->getData();
			}
			echo json_encode ($result);
		}else{
			echo json_encode ( array (
					'status' => '0x0004',
					'message' => 'no post'
				));
			return ;
		}
	}
	const DEFAULT_COMMENT_SORT_ORDER = 'created_time';
    const DEFAULT_COMMENT_SORT_DIR = 'desc';
	/* 
	* @Param int post_id
	* return json
	*/
	public function getPostAction(){
		$post_id = $this->getRequest()->getParam('post_id');
		$post = Mage::getModel('blog/post')->load($post_id);
		if(!$post->getId()){
			echo json_encode ( array (
					'status' => '0x0003',
					'message' => 'post not exists'
				));
			return ;
		}
		$result = $post->getData();
		$sortOrder = $this->getRequest()->getParam('order', self::DEFAULT_COMMENT_SORT_ORDER);
		$sortDirection = $this->getRequest()->getParam('dir', self::DEFAULT_COMMENT_SORT_DIR);
		$comments = Mage::getModel('blog/comment')
			->getCollection()
			->addPostFilter($post->getId()) 
			->addApproveFilter(2);
		$comments->setOrder($comments->getConnection()->quote($sortOrder), $sortDirection);
		//$comment->setPageSize((int)Mage::helper('blog')->commentsPerPage());
		$result['comments'] = count($comments);
		$response = Mage::helper('core')->jsonEncode($result);
		$this->getResponse()->setBody(urldecode($response));
	}
	
	/* 
	* @Param int post_id
	* return json
	*/
	public function getPostCommentAction(){
		
		$post_id = $this->getRequest()->getParam('post_id');
		$post = Mage::getModel('blog/post')->load($post_id);
		if(!$post->getId()){
			echo json_encode ( array (
					'status' => '0x0003',
					'message' => 'post not exists'
				));
			return ;
		}
		
		$sortOrder = $this->getRequest()->getParam('order', self::DEFAULT_COMMENT_SORT_ORDER);
		$sortDirection = $this->getRequest()->getParam('dir', self::DEFAULT_COMMENT_SORT_DIR);
		$comments = Mage::getModel('blog/comment')
			->getCollection()
			->addPostFilter($post->getId()) 
			->addApproveFilter(2);
		$comments->setOrder($comments->getConnection()->quote($sortOrder), $sortDirection);
		//$comment->setPageSize((int)Mage::helper('blog')->commentsPerPage());
		$comment = array();
		if(count($comments)>0){
			foreach($comments as $c){
				$data = $c->getData();
				$newdata = array();
				foreach($data as $index=>$d){
					$newdata[$index] = urlencode($d);
				}
				$comment[] = $newdata;
			}
		}
		$response = Mage::helper('core')->jsonEncode($comment);
		$this->getResponse()->setBody(urldecode($response));
	}
	
	/* 
	* @Param int post_id
	* @Param string user
	* @Param string email
	* @Param string comment
	* 
	*/
	public function addCommentAction(){
		$data = $this->getRequest()->getParams();
		$errors = $this->_validateData($data);
		if (!empty($errors)) {
			$errors['status'] = '0x0002';
			echo json_encode($errors);
			return;
		}
		$model = Mage::getModel('blog/comment');
		$model->setUser($data['user']);
        $model->setEmail($data['email']);
		$model->setComment(htmlspecialchars($data['comment'], ENT_QUOTES));
		$model->setPostId($data['post_id']);
		$model->setCreatedTime(now());
		$session = Mage::getSingleton('customer/session');
		if(Mage::getStoreConfig('blog/comments/approval')) {
				$model->setStatus(2);
		} else {
			if ($session->isLoggedIn() && Mage::getStoreConfig('blog/comments/loginauto')) {
				$model->setStatus(2);
			} else {
				$model->setStatus(1);
			}
		}
		$model->save();
		echo json_encode ( array (
					'status' => true,
					'message' => 'comment save successfully!'
				));
	}
	
	protected function _validateData($data)
    {
        $errors = array();
        $helper = Mage::helper('blog');

        if (!Zend_Validate::is($data['user'], 'NotEmpty')) {
            $errors[] = $helper->__('Name can\'t be empty');
        }

        if (!Zend_Validate::is($data['comment'], 'NotEmpty')) {
            $errors[] = $helper->__('Comment can\'t be empty');
        }

        if (!Zend_Validate::is($data['post_id'], 'NotEmpty')) {
            $errors[] = $helper->__('post_id can\'t be empty');
        }

        $validator = new Zend_Validate_EmailAddress();
        if (!$validator->isValid($data['email'])) {
            $errors[] = $helper->__('"%s" is not a valid email address.', $data['email']);
        }

        return $errors;
    }
} 