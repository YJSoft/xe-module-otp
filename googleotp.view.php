<?php

class googleotpView extends googleotp
{
	function init()
	{
		$config = $this->getConfig();
		
		$template_path = sprintf("%sskins/%s/", $this->module_path, $config->skin);
		if (!is_dir($template_path) || !$config->skin)
		{
			$config->skin = 'default';
			$template_path = sprintf("%sskins/%s/", $this->module_path, $config->skin);
		}
		
		$this->setTemplatePath($template_path);
		$this->setTemplateFile(strtolower(str_replace('dispGoogleotp', '', $this->act)));
	}

	function dispGoogleotpUserConfig()
	{
		if (!Context::get("is_logged"))
		{
			return $this->createObject(-1, "로그인해주세요");
		}
		if (Context::get('logged_info')->is_admin === "Y")
		{
			$member_srl = Context::get('member_srl') ? Context::get('member_srl') : Context::get('logged_info')->member_srl;
		}
		else
		{
			$member_srl = Context::get('logged_info')->member_srl;
		}

		$oGoogleOTPModel = getModel('googleotp');
		$domain = parse_url(getFullUrl());

		if (!$oGoogleOTPModel->checkUserConfig($member_srl))
		{
			$oGoogleOTPModel->insertNewConfig($member_srl);
		}
		$userconfig = $oGoogleOTPModel->getUserConfig($member_srl);
		$userconfig->qrcode = $oGoogleOTPModel->generateQRCode($domain['host'] . " - " . Context::get('logged_info')->user_id, $userconfig->otp_id);
		Context::set("user_config", $userconfig);
	}

	function dispGoogleotpInputotp()
	{
	}
}
