# basket

Not responsible for the correctness of the calculations. Calculations are based in Googling of UK accounting.  
Rounding is done at the Row level  

http://money.stackexchange.com/questions/73310/tax-for-basket-with-coupon-containing-two-different-vat-rates

##TODO
Finish BasketTest tests  
Write some RowTests  
Decide how coupons should be applied. To Items? To Rows? To a Basket? And what is the API to add a coupon? Via the basket?  
Think about recurring prices  

Add travis.yml  
Figure out lowest PHP version. 7?  
Figure out lowest version of PHPunit  


##Coupons
Possible coupons types  
X off whole basket  
X% off whole basket  
X off each of a certain item  
X% off each of a certain item  
Buy Y of a certain item get X off  
Buy Y of a certain item get X% off  
Buy Y of a certain item get X quantity of them free  
Buy X, Y and Z, get cheapest free  
Buy X, Z get Y free  
Are all of these coupons types? Or are some just logic?  
