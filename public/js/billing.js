var global = {};

$(function() {
    $( window ).on('beforeunload', function() {
        var valid= true;
        $('.panel input[type="text"]').each(function(){
            if($(this).val() != '') valid = false;
        });
        if(!valid){
            return "سوف تفقد الوارد إذا لم تقم بالضغط أولا على زر إضافة الوارد، هل تريد حقا ترك الصفحة؟";
        }
    });
    
    global.baseUrl = $('#baseUrl').val();
    global.categories = [];
    
    $("#saleDate").datetimepicker({
        pickTime: false,
        language: 'ar'
    });

    $("#addcategory").on("click", function () {
        var row =   $('.new-row tbody').html();
        $('.last-row').before(row);
        $.each($('td.row-id'), function(index){
            $(this).html(index+1);
        });
    });
    
    $('.table-category').on('click', 'input[name="category"]', function(){
        
        $('.table-category input[name="category"]').autocomplete({
            serviceUrl: global.baseUrl + '/category/get',
            minChars:1,
            delimiter: /(,|;)\s*/, // regex or character
            maxHeight:400,
            //width:300,
            zIndex: 9999,
            deferRequestBy: 0, //miliseconds
            triggerSelectOnValidInput: false,
            //params: { sale:false }, //aditional parameters
            noCache: true, //default is false, set to true to disable caching
            onSelect: function(category){
                var decoded = $("<div/>").html(category.value).text();
                $(this).val(decoded);
                $(this).attr('data-id',category.data)
                       .prop('disabled',true)
                       .addClass('text-center input-tagged')
                       .next().removeClass('hidden');
                var tds = $(this).closest('tr').children('td');
                
                $(tds).eq(2).find('span.category-quantity').html(category.categoryQuantity);
                if(category.categoryQuantity > 0)
                    $(tds).eq(2).find('input').attr('data-quantity' , category.categoryQuantity).val(1);
                else 
                    $(tds).eq(2).find('input').attr('data-quantity' , category.categoryQuantity).val(0);
                                      
                $(tds).eq(3).find('input').attr('data-price' , category.categorySellPrice)
                                          .val(category.categorySellPrice);
                // Calculate Total
                var total = 0, quantity=0;
                $('.table-category tr.category-row').each(function(index , category){
                    quantity += parseInt($(category).find('input[name="quantity"]').val());
                    total += parseInt($(category).find('input[name="quantity"]').val()) * parseInt($(category).find('input[name="sellPrice"]').val());
                });

                $('.total-quantity').html(quantity);
                $('.total-due').html(total);
            }
        });
        
    }).on('click' , '.remove-category', function(){
        if($('.table-category .category-row').length > 1)
            $(this).closest('.category-row').remove();
        $.each($('td.row-id'), function(index){
            $(this).html(index+1);
        });
    }).on('click' , '.edit-category' , function(){
        $(this).prev().prop('disabled',false)
                      .removeClass('text-center input-tagged')
                      .focus()
                      .next().addClass('hidden');
              
    }).on('change' , '.category-value' , function(){
        // Calculate Total
        var total = 0, quantity=0;
        $('.table-category tr.category-row').each(function(index , category){
            quantity += parseInt($(category).find('input[name="quantity"]').val());
            total += parseInt($(category).find('input[name="quantity"]').val()) * parseInt($(category).find('input[name="sellPrice"]').val());
        });

        $('.total-quantity').html(quantity);
        $('.total-due').html(total);
    });
    
    $('#saleForm').submit(function(e){
        e.preventDefault();
        var valid = true;
        
        $('ul.errors').remove();
        $('.table-category .category-row').each(function(index, row){
            var category = {};
            category['categoryId'] = $(row).find('input[name="category"]').attr('data-id');
            category['categoryQuantity'] = $(row).find('input[name="quantity"]').val();
            category['categorySellPrice'] = $(row).find('input[name="sellPrice"]').val();
            
            if(    !isInteger(category['categoryId'])
                || !isInteger(category['categoryQuantity'])
                || !isFloat(category['categorySellPrice'])
                ) { valid = false; }
                
            console.log(category);
            console.log(valid);
            if(!valid) return valid;
            global.categories[index] = category;
        });
        
        if(valid){
            $('#saleCategories').val(JSON.stringify(global.categories));
            var saleCategories = $('#saleCategories').val(),
                saleDiscount = $('#saleDiscount').val(),
                saleDate = $('#saleDate').val(),
                saleCustomer = {};
        
                saleCustomer['customerName']  = $('#customerName').val();
                saleCustomer['customerPhone'] = $('#customerPhone').val();
                saleCustomer['customerNotes'] = $('#customerNotes').val();
                
                
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: global.baseUrl + '/sale/add',
                async: false,
                data: { saleCategories: saleCategories,
                        saleDiscount: saleDiscount, 
                        saleDate: saleDate,
                        saleCustomer: JSON.stringify(saleCustomer) },
                success: function(json) {
                    if(json.errors){
                        $.each(json.errors, function(field, msg){
                            $('#'+field).after('<ul class="errors"><li>'+msg.isEmpty+'</li></ul>');
                        });
                        
                    } else {
                        $(window).unbind('beforeunload');
                        window.open(json.redirectUrl, '_blank');
                        window.location.reload(true);
                    }
                }
            });
            
        } else {
            $('.table-category').after('<ul class="errors"><li>يجب ملئ كل الحقول </li></ul>');
        }
    });
    
    $('.number').attr('type', 'number')
    .on('keyup', function(){
        $(this).removeClass('errors');
        var value = $(this).val();
        
        if( $(this).hasClass('float') )
            if( !isFloat(value) ) $(this).addClass('errors');
        else
            if( !isInteger(value) ) $(this).addClass('errors');
    });
    
    function isInteger(value) { return /^\d+$/.test(value); }
    function isFloat(value)   { return /^\d+(\.\d*){0,1}$/.test(value); }
    function isPositive(value){ return (value >= 0); }
});