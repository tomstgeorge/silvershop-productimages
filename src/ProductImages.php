<?php

namespace AntonyThorpe\SilverShopProductImages;

use SilverStripe\Core\Extension;
use SilverStripe\ORM\ManyManyList;
use SilverShop\Page\Product;
use SilverStripe\Forms\FieldList;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Model\List\ArrayList;
use SilverStripe\ORM\DataList;
use Bummzack\SortableFile\Forms\SortableUploadField;

/**
 * Extends SilverShop\Page\Product
 * @method ManyManyList<Image> AdditionalImages()
 * @extends Extension<(Product & static)>
 */
class ProductImages extends Extension
{
    /**
     * @config
     */
    private static array $many_many = [
        'AdditionalImages' => Image::class,
    ];

    /**
     * @config
     */
    private static array $many_many_extraFields = [
        'AdditionalImages' => ['SortOrder' => 'Int'],
    ];

    public function updateCMSFields(FieldList $fieldList): void
    {
        $newfields = [
            SortableUploadField::create(
                'AdditionalImages',
                _t(self::class . '.AdditionalImages', 'Additional Images')
            ),
            LiteralField::create(
                'additionalimagesinstructions',
                '<p>' . _t(self::class . '.Instructions', 'You can change the order of the Additional Images by clicking and dragging on the image thumbnail.') . '</p>'
            )
        ];
        if ($fieldList->hasTabset()) {
            $fieldList->addFieldsToTab('Root.Images', $newfields);
        } else {
            foreach ($newfields as $newfield) {
                $fieldList->push($newfield);
            }
        }
    }

    /**
     * Combines the main image and the secondary images
     */
    public function getAllImages(): ArrayList
    {
        $arrayList = ArrayList::create(
            $this->getOwner()->AdditionalImages()->sort('SortOrder')->toArray()
        );
        $main = $this->getOwner()->Image();
        if ($main->exists()) {
                $arrayList->unshift($main);
        }

        return $arrayList;
    }

    /**
     * Sorted images
     */
    public function getSortedAdditionalImages(): DataList
    {
        return $this->getOwner()->AdditionalImages()->sort('SortOrder');
    }
}
