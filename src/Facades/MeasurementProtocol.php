<?php

namespace App\Facades;

use HappyHorizon\GA\MeasurementProtocol\Contracts\Events\MeasurementProtocolEventContract;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\AddPaymentInfo;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\AddShippingInfo;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\AddToCart;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\AddToWishlist;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\BeginCheckout;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\Custom;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\EarnVirtualCurrency;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\GenerateLead;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\JoinGroup;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\LevelUp;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\Login;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\PostScore;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\Purchase;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\Refund;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\RemoveFromCart;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\Search;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\SelectContent;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\SelectItem;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\SelectPromotion;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\Share;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\SignUp;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\SpendVirtualCurrency;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\TutorialBegin;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\TutorialComplete;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\UnlockAchievement;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\ViewCart;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\ViewItem;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\ViewItemList;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\ViewPromotion;
use HappyHorizon\GA\MeasurementProtocol\Models\Events\ViewSearchResults;

class MeasurementProtocol
{
    public static function addPaymentInfoEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new AddPaymentInfo(...$args)
        );
    }

    public static function addShippingInfoEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new AddShippingInfo(...$args)
        );
    }

    public static function addToCartEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new AddToCart(...$args)
        );
    }

    public static function addToWishlistEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new AddToWishlist(...$args)
        );
    }

    public static function beginCheckoutEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new BeginCheckout(...$args)
        );
    }

    public static function customEvent(string $eventName, ...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new Custom($eventName, ...$args)
        );
    }

    public static function earnVirtualCurrencyEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new EarnVirtualCurrency(...$args)
        );
    }

    public static function generateLeadEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new GenerateLead(...$args)
        );
    }

    public static function joinGroupEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new JoinGroup(...$args)
        );
    }

    public static function levelUpEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new LevelUp(...$args)
        );
    }

    public static function loginEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new Login(...$args)
        );
    }

    public static function PostScoreEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new PostScore(...$args)
        );
    }

    public static function purchaseEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new Purchase(...$args)
        );
    }

    public static function refundEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new Refund(...$args)
        );
    }

    public static function removeFromCartEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new RemoveFromCart(...$args)
        );
    }

    public static function searchEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new Search(...$args)
        );
    }

    public static function selectContentEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new SelectContent(...$args)
        );
    }

    public static function selectItemEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new SelectItem(...$args)
        );
    }

    public static function selectPromotionEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new SelectPromotion(...$args)
        );
    }

    public static function shareEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new Share(...$args)
        );
    }

    public static function signUpEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new SignUp(...$args)
        );
    }

    public static function spendVirtualCurrencyEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new SpendVirtualCurrency(...$args)
        );
    }

    public static function tutorialBeginEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new TutorialBegin(...$args)
        );
    }

    public static function tutorialCompleteEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new TutorialComplete(...$args)
        );
    }

    public static function unlockAchievementEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new UnlockAchievement(...$args)
        );
    }

    public static function viewCartEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new ViewCart(...$args)
        );
    }

    public static function viewItemEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new ViewItem(...$args)
        );
    }

    public static function viewItemListEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new ViewItemList(...$args)
        );
    }

    public static function viewPromotionEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new ViewPromotion(...$args)
        );
    }

    public static function viewSearchResultEvent(...$args): void
    {
        Event::dispatch(
            MeasurementProtocolEventContract::class,
            new ViewSearchResults(...$args)
        );
    }
}