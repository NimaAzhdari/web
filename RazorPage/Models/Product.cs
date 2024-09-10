namespace MvcRazor.Models;

public class Product
{
    public int Id { get; set; }
    public string Code { get; set; }
    public string Name { get; set; }
    public string Description { get; set; }
    public string DescriptionLong { get; set; }
    public decimal Price { get; set; }
    public string type { get; set; }
    public string ImageSm { get; set; }
    public string ImageMd { get; set; }

}
