
using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.EntityFrameworkCore;
using MvcRazor.Data;
using MvcRazor.Models;
using System.Text.Json;
using System.ComponentModel.DataAnnotations;

namespace MvcRazor.Pages;

    public class ProductModel : PageModel
    {
        private readonly ShopContext _context;
        public ProductModel(ShopContext context){_context=context;}

        //get data from form
        [BindProperty,Range(1, int.MaxValue, ErrorMessage = "حداقل سفارش 1 هست")]
        public int Quantity_form { get; set; } = 1;
        [BindProperty]
        public string ProductCode_form { get; set; }
        //
        public Product Product { get; set; }
        public async Task<IActionResult> OnGetAsync()
        {
            string Code=Request.RouteValues["code"]?.ToString();
            Product =await _context.Products.FirstOrDefaultAsync(p => p.Code == Code);
            return Page();
        }
        public async Task<IActionResult> OnPostAsync()
        {
             
            string Code=Request.RouteValues["code"]?.ToString();
            Product =await _context.Products.FirstOrDefaultAsync(p => p.Code == Code);
             if(ModelState.IsValid)
             {
            Basket basket = new ();
            if(Request.Cookies[nameof(Basket)] is not null)
            {
                basket = JsonSerializer.Deserialize<Basket>(Request.Cookies[nameof(Basket)]);
            }
            var existingItem =basket.Items.FirstOrDefault(x => x.ProductCode == ProductCode_form);
            if (existingItem != null)
            {
               existingItem.Quantity += Quantity_form;
               
            }
            else//if its new item
            {
             basket.Items.Add(new OrderItem 
            { 
             ProductCode = ProductCode_form, 
             Quantity = Quantity_form,
             Price=Product.Price
            });   
            }
            
         var json = JsonSerializer.Serialize(basket);
         Response.Cookies.Append(nameof(Basket), json);
         return Page();
             }
             return Page();
        }
    }



