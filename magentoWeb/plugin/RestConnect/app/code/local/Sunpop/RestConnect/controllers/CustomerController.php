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
class Sunpop_RestConnect_CustomerController extends Mage_Core_Controller_Front_Action {
	const XML_PATH_REGISTER_EMAIL_TEMPLATE = 'customer/create_account/email_template';
	const XML_PATH_REGISTER_EMAIL_IDENTITY = 'customer/create_account/email_identity';
	const XML_PATH_REMIND_EMAIL_TEMPLATE = 'customer/password/remind_email_template';
	const XML_PATH_FORGOT_EMAIL_TEMPLATE = 'customer/password/forgot_email_template';
	const XML_PATH_FORGOT_EMAIL_IDENTITY = 'customer/password/forgot_email_identity';
	const XML_PATH_DEFAULT_EMAIL_DOMAIN         = 'customer/create_account/email_domain';
	const XML_PATH_IS_CONFIRM                   = 'customer/create_account/confirm';
	const XML_PATH_CONFIRM_EMAIL_TEMPLATE       = 'customer/create_account/email_confirmation_template';
	const XML_PATH_CONFIRMED_EMAIL_TEMPLATE     = 'customer/create_account/email_confirmed_template';
	const XML_PATH_GENERATE_HUMAN_FRIENDLY_ID   = 'customer/create_account/generate_human_friendly_id';
	public function statusAction() {
		$customerinfo = array ();
		if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
			$customer = Mage::getSingleton ( 'customer/session' )->getCustomer ();
			$storeUrl = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA); 
			$avatar = $customer->getMyAvatar (); 
			if (isset($avatar))
				$avatar = $storeUrl . "customer" . $customer->getMyAvatar ();
			$customerinfo = array (
					'name' => $customer->getName (),
					'email' => $customer->getEmail (),
					'avatar' => $avatar,
					'tel' => $customer->getDefaultMobileNumber () 
			);
			echo json_encode ( $customerinfo );
		} else
			echo 'false';
	}
	public function loginAction() {
		$session = Mage::getSingleton ( 'customer/session' );
		if (Mage::getSingleton ( 'customer/session' )->isLoggedIn ()) {
			$session->logout ();
		}
		$username = Mage::app ()->getRequest ()->getParam ( 'username' );
		$password = Mage::app ()->getRequest ()->getParam ( 'password' );
		try {
			if (! $session->login ( $username, $password )) {
				echo 'wrong username or password.';
			} else {
				echo $this->statusAction ();
			}
		} catch ( Mage_Core_Exception $e ) {
			switch ($e->getCode ()) {
				case Mage_Customer_Model_Customer::EXCEPTION_EMAIL_NOT_CONFIRMED :
					$value = Mage::helper ( 'customer' )->getEmailConfirmationUrl ( $uname );
					$message = Mage::helper ( 'customer' )->__ ( 'This account is not confirmed. <a href="%s">Click here</a> to resend confirmation email.', $value );
					echo json_encode ( array (
							'code' => $e->getCode (),
							'message' => $message 
					) );
					break;
				case Mage_Customer_Model_Customer::EXCEPTION_INVALID_EMAIL_OR_PASSWORD :
					$message = $e->getMessage ();
					echo json_encode ( array (
							'code' => $e->getCode (),
							'message' => $message 
					) );
					break;
				default :
					$message = $e->getMessage ();
					echo json_encode ( array (
							'code' => $e->getCode (),
							'message' => $message 
					) );
			}
		}
	}
	public function registerAction() {
		$params = Mage::app ()->getRequest ()->getParams ();
		
		$session = Mage::getSingleton ( 'customer/session' );
		$session->setEscapeMessages ( true );
		
		$customer = Mage::registry ( 'current_customer' );
		
		$errors = array ();
		if (is_null ( $customer )) {
			$customer = Mage::getModel ( 'customer/customer' )->setId ( null );
		}
		if (isset ( $params ['isSubscribed'] )) {
			$customer->setIsSubscribed ( 1 );
		}
		if( (null==Mage::app ()->getRequest ()->getParam ('password') ) || (null==Mage::app ()->getRequest ()->getParam ('email')) ){
			echo json_encode ( array (
					false,
					'0x1100',
					'empty password or email.'
			) );
			return ;
		}
		$customer->getGroupId ();
		try {
			//中文姓名处理，如果有 chinesename字段
			if ( $params ['chinesename'] == null  )	{
				$customer->setData ( 'firstname', $params ['firstname'] );
				$customer->setData ( 'lastname', $params ['lastname'] );
				}
			else	{
				$customer->setData ( 'lastname', mb_substr($params ['chinesename'], 0, 1, 'utf-8') );
				$customer->setData ( 'firstname', mb_substr($params ['chinesename'], 1, mb_strlen($params ['chinesename'])-1, 'utf-8') );
				}
			$customer->setPassword ( $params ['password'] );
			$customer->setConfirmation ( $this->getRequest ()->getPost ( 'confirmation', $params ['password'] ) );
			$customer->setData ( 'email', $params ['email'] );
			$customer->setData ( 'gender', $params ['gender'] );
			$customer->setData ( 'default_mobile_number', $params ['default_mobile_number'] );
			$validationResult = count ( $errors ) == 0;
			if (true === $validationResult) {
				$customer->save ();
				if ($customer->isConfirmationRequired ()) {
					$customer->sendNewAccountEmail ( 'confirmation', $session->getBeforeAuthUrl (), Mage::app ()->getStore ()->getId () );
				} else {
					$session->setCustomerAsLoggedIn ( $customer );
					$customer->sendNewAccountEmail ( 'registered', '', Mage::app ()->getStore ()->getId () );
				}
				
				$addressData = $session->getGuestAddress ();
				if ($addressData && $customer->getId ()) {
					$address = Mage::getModel ( 'customer/address' );
					$address->setData ( $addressData );
					$address->setCustomerId ( $customer->getId () );
					$address->save ();
					$session->unsGuestAddress ();
				}
				
				echo json_encode ( array (
						true,
						'0x0000',
						array () 
				) );
			} else {
				echo json_encode ( array (
						false,
						'0x1000',
						$errors 
				) );
			}
		} catch ( Mage_Core_Exception $e ) {
			if ($e->getCode () === Mage_Customer_Model_Customer::EXCEPTION_EMAIL_EXISTS) {
				$url = Mage::getUrl ( 'customer/account/forgotpassword' );
				$message = $this->__ ( 'There is already an account with this email address. If you are sure that it is your email address, <a href="%s">click here</a> to get your password and access your account.', $url );
				$session->setEscapeMessages ( false );
			} else {
				$message = $this->__( $e->getMessage () );
				//中文翻译有问题，故手工代码
				$message = str_replace("Please specify different value for","存在相同的注册信息，请输入一个不同的值", $message);
				$message = str_replace("attribute. Customer with such value already exists.","。", $message);

			}
			echo json_encode ( array (
					false,
					'0x1000',
					array (
							$message
					) 
			) );
		} catch ( Exception $e ) {
			echo json_encode ( array (
					false,
					'0x1000',
					$this->__( $e->getMessage () )
			) );
		}
	}
	public function forgotpwdAction() {
		$email = Mage::app ()->getRequest ()->getParam ( 'email' );
		$session = Mage::getSingleton ( 'customer/session' );
		$customer = Mage::registry ( 'current_customer' );
		if (is_null ( $customer )) {
			$customer = Mage::getModel ( 'customer/customer' )->setId ( null );
		}
 		if ($this->_user_isexists ( $email )) {
			$customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
			$this->_sendEmailTemplate ( $customer,self::XML_PATH_FORGOT_EMAIL_TEMPLATE, self::XML_PATH_FORGOT_EMAIL_IDENTITY, array (
					'customer' => $customer 
			), $storeId );
			echo json_encode ( array (
					'code' => '0x0000',
					'message' => 'Request has sent to your Email.'
			) );
		} else
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'No matched email data.' 
			) );
	}
	public function logoutAction() {
		try {
			Mage::getSingleton ( 'customer/session' )->logout();
			echo json_encode(array(true, '0x0000', null));
		} catch (Exception $e) {
			echo json_encode(array(false, '0x1000', $e->getMessage()));
		}
	}
	protected function _user_isexists($email) {
		$info = array ();
		$customer = Mage::getModel ( 'customer/customer' )->setWebsiteId ( Mage::app ()->getStore ()->getWebsiteId () )->loadByEmail ( $email );
		$info ['uname_is_exist'] = $customer->getId () > 0;
		$result = array (
				true,
				'0x0000',
				$info 
		);
		return $customer->getId () > 0;
	}
	protected function _sendEmailTemplate($customer,$template, $sender, $templateParams = array(), $storeId = null)
	{
		/** @var $mailer Mage_Core_Model_Email_Template_Mailer */
		$mailer = Mage::getModel('core/email_template_mailer');
		$emailInfo = Mage::getModel('core/email_info');
		$emailInfo->addTo($customer->getEmail(), $customer->getName());
		$mailer->addEmailInfo($emailInfo);
	
		// Set all required params and send emails
		$mailer->setSender(Mage::getStoreConfig($sender, $storeId));
		$mailer->setStoreId($storeId);
		$mailer->setTemplateId(Mage::getStoreConfig($template, $storeId));
		$mailer->setTemplateParams($templateParams);
		$mailer->send();
		return $this;
	}

} 