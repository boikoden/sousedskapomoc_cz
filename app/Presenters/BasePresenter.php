<?php

declare(strict_types=1);

namespace SousedskaPomoc\Presenters;

use Nette;
use SousedskaPomoc\Model\OrderManager;
use SousedskaPomoc\Model\UserManager;


/**
 * Base presenter for all application presenters.
 */
abstract class BasePresenter extends Nette\Application\UI\Presenter
{
    /** @persistent */
    public $locale;

    /** @var \Kdyby\Translation\Translator @inject */
    public $translator;

    /** @var UserManager */
    protected $userManager;

    /** @var OrderManager */
    protected $orderManager;



    public function injectOrderManager(OrderManager $orderManager)
    {
        $this->orderManager = $orderManager;
    }



    public function injectUserManager(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }



    public function beforeRender()
    {
        if ($this->user->isLoggedIn()) {
            $this->template->availableCouriers = $this->userManager->fetchAvailableCouriers();
        }

        $this->template->addFilter('getTranslation', function ($string) {
            return $this->translator->trans($string);
        });

        $this->template->addFilter('fetchUser', function ($courierId) {
            return $this->userManager->fetchCourierName($courierId);
        });

        $this->template->addFilter('humanFriendlyStatus', function ($status) {

            $statusList = [
                'new' => 'Nová',
                'assigned' => 'Přiřazená',
                'picking' => 'Kurýr vyzvedává',
                'delivering' => 'Kurýr doručuje',
                'delivered' => 'Kurýr doručil',
            ];

            return $statusList[$status] ?? $status;
        });
    }
}
