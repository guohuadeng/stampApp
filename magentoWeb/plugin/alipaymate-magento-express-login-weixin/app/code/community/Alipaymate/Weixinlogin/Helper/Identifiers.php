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
				$avatarpath = "/attached/attachment/download/customer/".$customer_id."/file/";
				$savepath = "/attached/attachment/download/customer/".$customer_id."/file/avatar.jpg";
				$creatpath = "./attached/attachment/download/customer/".$customer_id."/file/";
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

}
