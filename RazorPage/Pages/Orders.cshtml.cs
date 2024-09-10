using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.EntityFrameworkCore;
using MvcRazor.Data;
using MvcRazor.Models;
using System.Text.Json;

namespace MvcRazor.Pages;

    public class OrdersModel : PageModel
    {
        private readonly ShopContext _context;
        public OrdersModel(ShopContext context){_context=context;}
        
        public Basket basket { get; set; } = new ();
        public List<Product> SelectedProducts { get; set; } = new ();
        
        public async Task OnGetAsync()
        {
            if(Request.Cookies[nameof(Basket)] is not null)
            {
                 basket = JsonSerializer.Deserialize<Basket>(Request.Cookies[nameof(Basket)]);

                 var selectedProducts = basket.Items.Select(x => x.ProductCode).ToArray();
                 SelectedProducts = await _context.Products.Where(p => selectedProducts.Contains(p.Code)).ToListAsync();
            }

        }
    }

