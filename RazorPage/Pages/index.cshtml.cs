using Microsoft.AspNetCore.Mvc;
using Microsoft.AspNetCore.Mvc.RazorPages;
using Microsoft.EntityFrameworkCore;
using MvcRazor.Data;
using MvcRazor.Models;

namespace MvcRazor.Pages;

    public class indexModel : PageModel
    {
        private readonly ShopContext _context;
        public indexModel(ShopContext context){_context=context;}
        public List<category_product> Category { get; set; }=[];
        public List<vitrin_product> Vitrin { get; set; }=[];
        public List<montakhab_product> Montakhab  { get; set; }=[];
        public List<Product> MontakhabData  { get; set; }=[];
        public List<Product> VitrinData  { get; set; }=[];
        public async Task OnGetAsync()
        {
            Category=await _context.CategoryProduct.ToListAsync();
            Vitrin= await _context.VitrinProduct.ToListAsync();
            Montakhab= await _context.MontakhabProduct.ToListAsync();
            foreach(var data in Vitrin )
            {
                
                VitrinData.Add(await _context.Products.FirstOrDefaultAsync(p => p.Code == data.Code));

            }
            foreach(var data in Montakhab )
            {
                MontakhabData.Add(await _context.Products.FirstOrDefaultAsync(p => p.Code == data.Code));
            }
        }
    }

