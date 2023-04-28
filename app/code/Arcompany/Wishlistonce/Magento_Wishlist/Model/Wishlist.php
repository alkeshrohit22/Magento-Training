<?php
namespace Arcompany\Wishlistonce\Magento_Wishlist\Model;

use Magento\Framework\Exception\LocalizedException;

class Wishlist extends \Magento\Wishlist\Model\Wishlist
{
    /**
     * Add new item
     * @param   \Magento\Catalog\Model\Product $product
     * @param   float|int|string $buyRequest
     * @return  \Magento\Wishlist\Model\Item
     */

    public function addNewItem($product, $buyRequest = null, $forciblySetQty = false)
    {
        // Get wishlist items
        $wishlistItems = $this->getItemCollection()->getData();

        // Check if product already exists in the wishlist
        foreach ($wishlistItems as $item) {
            if ($item['product_id'] == $product->getId()) {

                // If product exists, throw an error and redirect to the product page
                throw new LocalizedException(__('This product is already in your wishlist.'));
                return false;

            }
        }

        // If product does not exist, add the new item
        return parent::addNewItem($product, $buyRequest, $forciblySetQty);
    }
}
