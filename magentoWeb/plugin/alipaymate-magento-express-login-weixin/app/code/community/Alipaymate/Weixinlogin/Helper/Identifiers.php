<?php
class Alipaymate_Weixinlogin_Helper_Identifiers extends Mage_Core_Helper_Abstract
{

    /**
     * Assigns a new identifier to a customer
     *
     * @param int $customer_id
     * @param string $identifier
     */
    public function saveIdentifier($data)
    {
        $openid        =  isset($data['openid']        ) ? $data['openid']        : '';
        $nickname      =  isset($data['nickname']      ) ? $data['nickname']      : '';
        $sex           =  isset($data['sex']           ) ? $data['sex']           :  0;
        $city          =  isset($data['city']          ) ? $data['city']          : '';
        $province      =  isset($data['province']      ) ? $data['province']      : '';
        $country       =  isset($data['country']       ) ? $data['country']       : '';
        $headimgurl       =  isset($data['headimgurl']       ) ? $data['headimgurl']       : '';
        $unionid       =  isset($data['unionid']       ) ? $data['unionid']       : '';
        $refresh_token =  isset($data['refresh_token'] ) ? $data['refresh_token'] : '';
        $inside_weixin =  isset($data['inside_weixin'] ) ? $data['inside_weixin'] :  0;

        $_helper = Mage::helper('weixinlogin');
        $_helper->log('weixinlogin-return saveIdentifier', $data);

        try {
            // get customer id
            $email     = $unionid . '@weixin.com';
            $firstName = $nickname;
            $lastName  = '先生';

            if ($sex == 2) {
                $lastName  = '女士';
            }

            $customer = Mage::getModel("customer/customer");
            $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
            $customer->loadByEmail($email);

            $customer_id = $customer->getId();

            // add new customer, if $email not exists
            if (empty($customer_id)) {
                $customer->setWebsiteId(Mage::app()->getWebsite()->getId())
                        ->setEmail($email)
                        ->setFirstname($firstName)
                        ->setLastname($lastName)
                        ->setWeixinToken($refresh_token)
                        ->setPassword($customer->generatePassword(10))
                        ->save();

                $customer->setConfirmation(null);
                $customer->save();
                $customer_id = $customer->getId();
				/* 微信头像图片保存到服务器指定目录 */
				$url = $headimgurl;
				$avatarpath = "/media/attached/attachment/download/customer/".$customer_id."/file/";
				$savepath = "/media/attached/attachment/download/customer/".$customer_id."/file/avatar.jpg";
				$creatpath = "./media/attached/attachment/download/customer/".$customer_id."/file/";
				if(!file_exists($creatpath))     mkdir($creatpath,0777,true);
				$imageurl = Mage::getBaseDir().$avatarpath.'avatar.jpg';
				$hander = curl_init();
				$fp = fopen($imageurl,'wb');
				curl_setopt($hander,CURLOPT_URL,$url);
				curl_setopt($hander,CURLOPT_FILE,$fp);
				curl_setopt($hander,CURLOPT_HEADER,0);
				curl_setopt($hander,CURLOPT_FOLLOWLOCATION,1);
				curl_setopt($hander,CURLOPT_TIMEOUT,60);
				curl_exec($hander);
				curl_close($hander);
				fclose($fp);

				/* 头像图片的路径保存到数据库对应的avatar字段 */
				$currcustomer = Mage::getModel('customer/customer')->load($customer_id);
				$currcustomer->setData ('wechat_avatar',$savepath );
				$currcustomer->save();
            }

            if (empty($customer_id)) {
                return false;
            }

            // save identifier
            $identifier = Mage::getModel('weixinlogin/identifiers');
            $identifier_id = $identifier->getCollection()->addFieldToFilter('unionid', $unionid)->getFirstItem()->getId();

            if (empty($identifier_id)) {
                $identifier_id = 0;
            }

            $_helper->log('weixinlogin-return identifier_id', $identifier_id);

            $identifier->load($identifier_id)
                ->setOpenid(       $openid)
                ->setNickname(     $nickname)
                ->setSex(          $sex )
                ->setCity(         $city)
                ->setProvince(     $province)
                ->setCountry(      $country)
                ->setUnionid(      $unionid)
                ->setHeadimgurl(      $headimgurl)
                ->setRefreshToken( $refresh_token)
                ->setCustomerId(   $customer_id)
                ->setInsideWeixin( $inside_weixin)
                ->save();

            return $customer;
        } catch (Exception $e) {
            $_helper->log('weixinlogin-return saveIdentifier Exception', $e->getMessage());
        }

        return false;
    }

	public function saveLoginWeixin($data){
		$openid        =  isset($data['openid']        ) ? $data['openid']        : '';
        $nickname      =  isset($data['nickname']      ) ? $data['nickname']      : '';
        $sex           =  isset($data['sex']           ) ? $data['sex']           :  0;
        $city          =  isset($data['city']          ) ? $data['city']          : '';
        $province      =  isset($data['province']      ) ? $data['province']      : '';
        $country       =  isset($data['country']       ) ? $data['country']       : '';
        $headimgurl       =  isset($data['headimgurl']       ) ? $data['headimgurl']       : '';
        $unionid       =  isset($data['unionid']       ) ? $data['unionid']       : '';
        $refresh_token =  isset($data['refresh_token'] ) ? $data['refresh_token'] : '';
        $inside_weixin =  isset($data['inside_weixin'] ) ? $data['inside_weixin'] :  0;

		 // save identifier
		$identifier = Mage::getModel('weixinlogin/identifiers');
		$identifier_id = $identifier->getCollection()->addFieldToFilter('unionid', $unionid)->getFirstItem()->getId();

		if (empty($identifier_id)) {
			$identifier_id = 0;
		}
		$customer_id = 0;
		$identifier->load($identifier_id)
			->setOpenid(       $openid)
			->setNickname(     $nickname)
			->setSex(          $sex )
			->setCity(         $city)
			->setProvince(     $province)
			->setCountry(      $country)
			->setUnionid(      $unionid)
			->setHeadimgurl(      $headimgurl)
			->setRefreshToken( $refresh_token)
			->setCustomerId(   $customer_id)
			->setInsideWeixin( $inside_weixin)
			->save();
		return true;
	}

	public function saveRestLoginWeixin($data){
		$openid        =  isset($data['openid']        ) ? $data['openid']        : '';
        $nickname      =  isset($data['nickname']      ) ? $data['nickname']      : '';
        $sex           =  isset($data['sex']           ) ? $data['sex']           :  0;
        $city          =  isset($data['city']          ) ? $data['city']          : '';
        $province      =  isset($data['province']      ) ? $data['province']      : '';
        $country       =  isset($data['country']       ) ? $data['country']       : '';
        $headimgurl       =  isset($data['headimgurl']       ) ? $data['headimgurl']       : '';
        $unionid       =  isset($data['unionid']       ) ? $data['unionid']       : '';
        $refresh_token =  isset($data['refresh_token'] ) ? $data['refresh_token'] : '';
        $inside_weixin =  isset($data['inside_weixin'] ) ? $data['inside_weixin'] :  0;

		 // save identifier
		$identifier = Mage::getModel('weixinlogin/identifiers');
		$identifier_id = $identifier->getCollection()->addFieldToFilter('unionid', $unionid)->getFirstItem()->getId();

		if (empty($identifier_id)) {
			$identifier_id = 0;
		}
		$customer_id = 0;
		$identifier->load($identifier_id)
			->setOpenid(       $openid)
			->setNickname(     $nickname)
			->setSex(          $sex )
			->setCity(         $city)
			->setProvince(     $province)
			->setCountry(      $country)
			->setUnionid(      $unionid)
			->setHeadimgurl(      $headimgurl)
			->setRefreshToken( $refresh_token)
			->setCustomerId(   $customer_id)
			->setInsideWeixin( $inside_weixin)
			->save();
		return $identifier;
	}

    /**
     * Gets a customer by identifier
     *
     * @param string $identifier
     * @return Mage_Customer_Model_Customer
     */
    public function getCustomer($identifier)
    {
        $collection = Mage::getModel('weixinlogin/identifiers')
            ->getCollection()
            ->addFieldToFilter('unionid', $identifier);

        if ($collection->getSize()) {
            $customer_id = $collection->getFirstItem()->getCustomerId();

            if (!empty($customer_id)) {
                $customer = Mage::getModel('customer/customer')
                    ->getCollection()
                    ->addFieldToFilter('entity_id', $customer_id)
                    ->getFirstItem();

                return $customer;
            }
        }

        return false;
    }


    public function getCustomerByEmail($email)
    {
        $customer = Mage::getModel("customer/customer");
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->loadByEmail($email);

        return $customer;
    }

	public function sendSms($mobil,$password){
		$url = "http://www.58stamp.com/smsapi/SendTemplateSMS.php?template=74318";
		$url = $url . '&mobile='. $mobil . '&validation=' . $password;

		$ch = curl_init();

		$timeout = 5;
		curl_setopt ($ch, CURLOPT_URL,$url);
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  // 遇到302自动跳转
		$response = curl_exec($ch);
		$responseData = json_decode($response, TRUE);
		return $responseData;
	}
	/* *
	* 判断当前customer_id是否已经绑定uniond
	* @param string $unionid int customer_id
	* @return array()  code = 1 是没有customer_id或unionid参数 ,code = 2 customer_id 没有改参数对应的customer,   code = 3 已经绑定, code = 4 参数正确没绑定, code = 5 unionid参数错误
 	*/
	public function checkUnionidBound($customer_id, $unionid){
		if(!$customer_id || !$unionid){
			return array(
				'status'=>false,
				'code' => 1
			);
		}

		$customercollection = Mage::getModel('customer/customer')
			->getCollection()
			->addFieldToFilter('entity_id', $customer_id);
		if ($customercollection->getSize()) {
			$collection = Mage::getModel('weixinlogin/identifiers')
				->getCollection()
				->addFieldToFilter('customer_id', $customer_id);
			if ($collection->getSize()) {
				return array(
						'status'=>true,
						'code' => 3
					);
			}else{
				$unionidcollection = Mage::getModel('weixinlogin/identifiers')
					->getCollection()
					->addFieldToFilter('unionid', $unionid);
				if ($unionidcollection->getSize()) {
					return array(
						'status'=>false,
						'code' => 4
					  );
				}else{
					return array(
						'status'=>false,
						'code' => 5
					  );
				}
			}
		}else{
			return array(
				'status'=>false,
				'code' => 2
			  );
		}



	}
}
