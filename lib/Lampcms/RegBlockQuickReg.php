<?php
/**
 *
 * License, TERMS and CONDITIONS
 *
 * This software is lisensed under the GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
 * Please read the license here : http://www.gnu.org/licenses/lgpl-3.0.txt
 *
 *  Redistribution and use in source and binary forms, with or without
 *  modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. The name of the author may not be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 *
 * ATTRIBUTION REQUIRED
 * 4. All web pages generated by the use of this software, or at least
 * 	  the page that lists the recent questions (usually home page) must include
 *    a link to the http://www.lampcms.com and text of the link must indicate that
 *    the website's Questions/Answers functionality is powered by lampcms.com
 *    An example of acceptable link would be "Powered by <a href="http://www.lampcms.com">LampCMS</a>"
 *    The location of the link is not important, it can be in the footer of the page
 *    but it must not be hidden by style attibutes
 *
 * THIS SOFTWARE IS PROVIDED BY THE AUTHOR "AS IS" AND ANY EXPRESS OR IMPLIED
 * WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF
 * MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
 * IN NO EVENT SHALL THE FREEBSD PROJECT OR CONTRIBUTORS BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF
 * THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This product includes GeoLite data created by MaxMind,
 *  available from http://www.maxmind.com/
 *
 *
 * @author     Dmitri Snytkine <cms@lampcms.com>
 * @copyright  2005-2011 (or current year) ExamNotes.net inc.
 * @license    http://www.gnu.org/licenses/lgpl-3.0.txt GNU LESSER GENERAL PUBLIC LICENSE (LGPL) version 3
 * @link       http://www.lampcms.com   Lampcms.com project
 * @version    Release: @package_version@
 *
 *
 */


namespace Lampcms;

use \Lampcms\Forms\Form;

/**
 * Generates html of the QuickRegistration block
 * it extends RegBlock
 * the main difference is that this class also inserts
 * the Captcha into form (2 hidden fields, one text field and
 * an image)
 *
 * @author Dmitri Snytkine
 *
 */
class RegBlockQuickReg extends RegBlock
{
	protected $aUsername = array(
	'usernameLabel' => 'Choose a Username',
	'usernameVal' => '',
	'usernameNote' => 'Username will appear alongside your posts');


	protected function setUsernameVars(){
		return $this;
	}

	
	protected function prepareVars(){

		$this->aVars = array(
		'titleBar' => 'Welcome to '.$this->Registry->Ini->SITE_NAME,
		'token' => Form::generateToken(), /*$this->oGlobal->addFormToken(),*/
		'title' => 'Welcome to '.$this->Registry->Ini->SITE_NAME,
		'header2' => '<div class="step2">Join us! It\'s super easy and free!</div>',
		'captcha' => $this->makeCaptchaBlock(),
		'action' => 'quickreg',
		'td1width' => '200px',
		'td2' => $this->makeSocialAuthBlock(),
		'loginBlock' => $this->makeLoginBlock()
		);

		$this->addUsernameBlock();

		return $this;
	}

	/**
	 *
	 * Add a small login block to the template
	 * but ONLY for ajax based request
	 *
	 * for regular web page we don't need
	 * to have yet another login block
	 * in the registration block
	 *
	 * @return string html of login block
	 * of empty string for non-ajax request
	 */
	protected function makeLoginBlock(){

		if(Request::isAjax()){
			return \tplLoginblock::parse(array());
		}

		return '';
	}

	/**
	 * Makes html for the captcha block, complete
	 * with image, input element for captcha
	 * and 2 hidden fields
	 *
	 * @return string HTML block
	 */
	protected function makeCaptchaBlock(){			
		$s = Captcha\Captcha::factory($this->Registry->Ini)->getCaptchaBlock();

		return $s;
	}

	
	/**
	 * Make html block for the external
	 * auth providers icons with links
	 * 
	 * @todo add LinkedIn button!
	 *
	 * @return string html
	 */
	public function makeSocialAuthBlock($or = '<h2>-OR-</h2>'){
		$s = '';
		$Ini = $this->Registry->Ini;
		$GfcSiteID = $Ini->GFC_ID;

		if(isset($Ini->TWITTER)){
			$aTW = $Ini['TWITTER'];
			if(!empty($aTW['TWITTER_OAUTH_KEY']) && !empty($aTW['TWITTER_OAUTH_SECRET'])){
				//onClick="oSL.Twitter.startDance(); return false"
				$s  .= '<div class="extauth"><a href="#" class="ajax twsignin"><img class="hand" src="/images/signin.png" width="151" height="24" alt="Sign in with Twitter account"/></a></div>';
			}
		}

		d('cp');
		if(isset($Ini->FACEBOOK)){
			$aFB = $Ini['FACEBOOK'];
			d('cp');
			if(!empty($aFB['APP_ID'])){
				d('cp');
				//onClick="oSL.initFBSignup(); return false"
				$s  .= '<div class="extauth"><a href="#" class="ajax fbsignup"><img class="hand" src="/images/fblogin.png" width="154" height="22" alt="Sign in with Facebook account"/></a></div>';
			}
		}

		if(!empty($GfcSiteID)){
			$s  .=  '<div class="extauth"><a href="#" onClick="google.friendconnect.requestSignIn(); return false;"><img class="hand" src="/images/gfcbutton.jpg" width="226" height="40" alt="Sign in with Google Friend Connect"/></a></div>';
		}

		$label = (!empty($s)) ? '<h3>Join with account you already have</h3><hr class="line1"/>' : '';
		
		return \tplSocial::parse(array($s, '', $label), false);
	}

}
