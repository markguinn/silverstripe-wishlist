Wishlist Submodule for Silverstripe Shop/Ecommerce
=====================================================

Allows customer to maintain multiple wishlists. Can be maintained
from the WishListPage on the frontend. *Supports both Shop and
Ecommerce modules.*

USAGE:
------
Install module via composer (markguinn/silverstripe-wishlist)
or the old-fashioned way. Add the following to mysite/_config/config.yml:

```
Product:
  extensions:
    - CanAddToWishList
```

Make sure to dev/build?flush=1. You can add this extension to
any Buyable model. It should work with non-sitetree models
but that has not been tested.

Depending on which module you're using you may need to include
the WishListButtons.ss template (shop) or update the ProductActionsInner.ss
template (ecommerce) in the spirit of the one included with this module.

*NOTE:* If you prefer the module to be installed explicitly as a
submodule for either ecommerce or shop, you can use dev-shop or
dev-ecommerce instead of dev-master. The only difference at this
time is the install folder being ecommerce_wishlist or shop_wishlist
instead of just wishlist.

TODO:
-----
- Add to alternate list from product page
- Ajaxify product page
- View in CMS
- "Save an idea" feature
- public/private lists
- share with link
- Apply to ProductVariations and test

DEVELOPERS:
-----------
* Mark Guinn - mark@adaircreative.com

Pull requests always welcome. Follow Silverstripe coding guidelines.


LICENSE (MIT):
--------------
Copyright (c) 2013 Mark Guinn

Permission is hereby granted, free of charge, to any person obtaining a copy of
this software and associated documentation files (the "Software"), to deal in
the Software without restriction, including without limitation the rights to use,
copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the
Software, and to permit persons to whom the Software is furnished to do so, subject
to the following conditions:

The above copyright notice and this permission notice shall be included in all copies
or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED,
INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR
PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE
FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
DEALINGS IN THE SOFTWARE.