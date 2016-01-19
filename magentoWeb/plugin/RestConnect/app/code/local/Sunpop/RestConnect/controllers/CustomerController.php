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
			$avatar = $customer->getAvatar ();
			if (isset($avatar))
				$avatar = $storeUrl . "customer" . $customer->getAvatar ();
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
				echo Mage::helper ( 'customer' )->__ ('Invalid login or password.');
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
					Mage::helper ( 'customer' )->__ ('empty password or email.')
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
			$customer->setData ( 'avatar', $params ['avatar'] );
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
				$message = Mage::helper ( 'customer' )->__ ( $e->getMessage () );
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
					'message' => Mage::helper ( 'customer' )->__ ('Request has sent to your Email.')
			) );
		} else
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => Mage::helper ( 'customer' )->__ ('No matched email data.')
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

	public function infoAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
            echo json_encode ( array (
					'code' => '0x0001',
					'message' => Mage::helper ( 'customer' )->__ ('not_exists')
			));
			return ;
        }

        if (!is_null($attributes) && !is_array($attributes)) {
            $attributes = array($attributes);
        }

        $result = array();
		$result['customer_id'] = $customer->getId();
		$resource = new Mage_Customer_Model_Api_Resource;
        foreach ($resource->_mapAttributes as $attributeAlias=>$attributeCode) {
            $result[$attributeAlias] = $customer->getData($attributeCode);
        }

        foreach ($resource->getAllowedAttributes($customer, $attributes) as $attributeCode=>$attribute) {
            $result[$attributeCode] = $customer->getData($attributeCode);
        }

        echo json_encode ( $result );
	}

	public function updateAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
           echo json_encode ( array (
					'code' => '0x0001',
					'message' => Mage::helper ( 'customer' )->__ ('not_exists')
			));
			return ;
        }
		$customerData = $this->getRequest ()->getParams();
		$resource = new Mage_Customer_Model_Api_Resource;
		foreach ($resource->getAllowedAttributes($customer) as $attributeCode=>$attribute) {
            if (isset($customerData[$attributeCode])) {
                $customer->setData($attributeCode, $customerData[$attributeCode]);
            }
        }

        $customer->save();
         echo json_encode ( array (
					'status' => true,
					'message' => Mage::helper ( 'customer' )->__ ('Save successfully')
			));
			return ;
	}

	public function chanagePasswordAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
           echo json_encode ( array (
					'code' => '0x0002',
					'message' => Mage::helper ( 'customer' )->__ ('customer_not_login')
			));
			return ;
        }

		$params = $this->getRequest ()->getParams();
		if(!count($params)){
			 echo json_encode ( array (
					'code' => '0x0003',
					'message' => Mage::helper ( 'customer' )->__ ('The password cannot be empty.')
			));
			return ;
		}
		if(!$params['oldpassword']){
			echo json_encode ( array (
					'code' => '0x0004',
					'message' => Mage::helper ( 'customer' )->__ ('The password cannot be empty.')
			));
			return ;
		}
		if(!$params['newpassword']){
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => Mage::helper ( 'customer' )->__ ('The password cannot be empty.')
			));
			return ;
		}
		$websiteId = $customer->getWebsiteId();
		$email = $customer->getEmail();
		$oldpassword = $params['oldpassword'];
		$newpassword = $params['newpassword'];
		$validate = 0;
		try {
			$login_customer_result = Mage::getModel('customer/customer')->setWebsiteId($websiteId)->authenticate($email, $oldpassword);
			$validate = 1;
		}
		catch(Exception $ex) {
			$validate = 0;
		}
		if($validate == 1) {
			 try {
				$customer->setPassword($newpassword);
				$customer->save();
				echo json_encode ( array (
						'code' => '0x0006',
						'message' => Mage::helper ( 'customer' )->__ ('Your Password has been Changed Successfully')
				));
				return ;
			 }
			 catch(Exception $ex) {
				echo json_encode ( array (
						'code' => '0x0006',
						'message' => $ex->getMessage()
				));
				return ;
			 }
		}
		else {
			echo json_encode ( array (
					'code' => '0x0006',
					'message' => Mage::helper ( 'customer' )->__ ('Invalid current password')
			));
			return ;
		}
		return ;
	}

	public function addressListAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'customer_not_exists'
			));
			return ;
        }

        $result = array();
		$resource = new Mage_Customer_Model_Api_Resource;
        foreach ($customer->getAddresses() as $address) {
            $data = $address->toArray();
            $row  = array();

            foreach ($resource->_mapAttributes as $attributeAlias => $attributeCode) {
                $row[$attributeAlias] = isset($data[$attributeCode]) ? $data[$attributeCode] : null;
            }

            foreach ($resource->getAllowedAttributes($address) as $attributeCode => $attribute) {
                if (isset($data[$attributeCode])) {
                    $row[$attributeCode] = $data[$attributeCode];
                }
            }
			$row['custome_address_id'] = $address->getId();
            $row['is_default_billing'] = $customer->getDefaultBilling() == $address->getId();
            $row['is_default_shipping'] = $customer->getDefaultShipping() == $address->getId();
			//$result['id']=$address->getId();
            $result[] = $row;

        }
        echo json_encode($result);
		return ;
	}

	public function addressCreateAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'customer_not_exists'
			));
			return ;
        }
		$addressData = $this->getRequest ()->getParams();
		$resource = new Mage_Customer_Model_Api_Resource;
		$address = Mage::getModel('customer/address');

        foreach ($resource->getAllowedAttributes($address) as $attributeCode=>$attribute) {
            if (isset($addressData[$attributeCode])) {
                $address->setData($attributeCode, $addressData[$attributeCode]);
            }
        }

        if (isset($addressData['is_default_billing'])) {
            $address->setIsDefaultBilling($addressData['is_default_billing']);
        }

        if (isset($addressData['is_default_shipping'])) {
            $address->setIsDefaultShipping($addressData['is_default_shipping']);
        }

        $address->setCustomerId($customer->getId());

        $valid = $address->validate();

        if (is_array($valid)) {
			echo json_encode ( array (
					'code' => '0x0001',
					'message' =>  implode("\n", $valid)
			));
			return ;
        }

        try {
            $address->save();
        } catch (Mage_Core_Exception $e) {
			echo json_encode ( array (
					'code' => '0x0001',
					'message' =>  $e->getMessage()
			));
			return ;
        }

		echo json_encode ( array (
					'status' => true,
					'message' => Mage::helper ( 'customer' )->__ ('Update successfully')
			));
			return ;
	}

	public function addressInfoAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'customer_not_exists'
			));
			return ;
        }
		$addressid = ( int ) $this->getRequest ()->getParam('addressid');
		$resource = new Mage_Customer_Model_Api_Resource;

		if($addressid){
			$address = Mage::getModel('customer/address')
				->load($addressid);

			if (!$address->getId()) {
				echo json_encode ( array (
					'code' => '0x0001',
					'message' => Mage::helper ( 'customer' )->__ ('address_not_exists')
				));
				return ;
			}
			if($address->getParentId() != $customer->getId()){
				echo json_encode ( array (
					'code' => '0x0002',
					'message' => Mage::helper ( 'customer' )->__ ('Addresses are not current customers')
				));
				return ;
			}
			$result = array();
			$result['customer_address_id'] = $addressid;
			foreach ($resource->_mapAttributes as $attributeAlias => $attributeCode) {
				$result[$attributeAlias] = $address->getData($attributeCode);
			}

			foreach ($resource->getAllowedAttributes($address) as $attributeCode => $attribute) {
				$result[$attributeCode] = $address->getData($attributeCode);
			}


			if ($customer = $address->getCustomer()) {
				$result['is_default_billing']  = $customer->getDefaultBilling() == $address->getId();
				$result['is_default_shipping'] = $customer->getDefaultShipping() == $address->getId();
			}
			echo json_encode($result);
			return;
		}else{
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'address_id_not_exists'
			));
			return ;
		}
	}

	public function addressUpdateAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'customer_not_exists'
			));
			return ;
        }
		$addressid = ( int ) $this->getRequest ()->getParam('addressid');
		$resource = new Mage_Customer_Model_Api_Resource;
		if($addressid){
			$address = Mage::getModel('customer/address')
				->load($addressid);

			if (!$address->getId()) {
				echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'address_not_exists'
				));
				return ;
			}
			if($address->getParentId() != $customer->getId()){
				echo json_encode ( array (
					'code' => '0x0002',
					'message' => Mage::helper ( 'customer' )->__ ('Addresses are not current customers')
				));
				return ;
			}
			$addressData = $this->getRequest ()->getParams();
			foreach ($resource->getAllowedAttributes($address) as $attributeCode=>$attribute) {
            if (isset($addressData[$attributeCode])) {
					$address->setData($attributeCode, $addressData[$attributeCode]);
				}
			}

			if (isset($addressData['is_default_billing'])) {
				$address->setIsDefaultBilling($addressData['is_default_billing']);
			}

			if (isset($addressData['is_default_shipping'])) {
				$address->setIsDefaultShipping($addressData['is_default_shipping']);
			}

			$valid = $address->validate();
			if (is_array($valid)) {
				$resource->_fault('data_invalid', implode("\n", $valid));
			}

			try {
				$address->save();
				echo json_encode ( array (
					'status' => true,
					'message' => Mage::helper ( 'customer' )->__ ('address update successfully')
				));
				return ;
			} catch (Mage_Core_Exception $e) {
				$resource->_fault('data_invalid', $e->getMessage());
			}
		}else{
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'address_id_not_exists'
			));
			return ;
		}
	}

	public function addressDeleteAction(){
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		if (!$customer->getId()) {
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'customer_not_exists'
			));
			return ;
        }
		$addressid = ( int ) $this->getRequest ()->getParam('addressid');
		$resource = new Mage_Customer_Model_Api_Resource;
		if($addressid){
			$address = Mage::getModel('customer/address')
				->load($addressid);

			if (!$address->getId()) {
				echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'address_not_exists'
				));
				return ;
			}
			if($address->getParentId() != $customer->getId()){
				echo json_encode ( array (
					'code' => '0x0002',
					'message' => Mage::helper ( 'customer' )->__ ('Addresses are not current customers')
				));
				return ;
			}

			try {
				$address->delete();
				echo json_encode ( array (
					'status' => true,
					'message' => 'address delete successfully'
				));
				return ;
			} catch (Mage_Core_Exception $e) {
				$resource->_fault('data_invalid', $e->getMessage());
			}
		}else{
			echo json_encode ( array (
					'code' => '0x0001',
					'message' => 'address_id_not_exists'
			));
			return ;
		}
	}
}
