<?php
/**
 * @author Sergey Bondarenko, <broken@propeople.com.ua>
 */
namespace Behat\Drupal\Propeople\Drush;

class DrushContext extends RawDrushContext
{
    /**
     * @Given /^(?:|I )login with one time link$/
     *
     * @drush @api
     */
    public function loginWithOneTimeLink()
    {
        $user_context = $this->getUserContext();

        if ($user_context->isLoggedIn()) {
            $this->logout();
        }

        $user = $user_context->createTestUser();
        // Care about not-configured Drupal installations, when
        // the "$base_url" variable is not set in "settings.php".
        // Also, remove the last underscore symbol from link for
        // prevent opening the page for reset the password;
        $link = rtrim($this->getBaseUrl() . parse_url($this->getOneTimeLoginLink($user->name), PHP_URL_PATH), '_');
        $this->getSession()->visit($this->locatePath($link));

        $text = t('You have just used your one-time login link.');
        if (!preg_match("/$text|$user->name/i", $this->getWorkingElement()->getText())) {
            throw new \Exception(sprintf('Cannot login with one time link: "%s"', $link));
        }
    }
}
