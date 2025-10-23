using Microsoft.EntityFrameworkCore;
using SmartERP.AdvancedModule.Models;

namespace SmartERP.AdvancedModule.Data
{
    public class ApplicationDbContext : DbContext
    {
        public ApplicationDbContext(DbContextOptions<ApplicationDbContext> options) 
            : base(options)
        {
        }

        // DbSets for reporting tables
        public DbSet<SalesReport> SalesReports { get; set; }
        public DbSet<ProductPerformance> ProductPerformances { get; set; }
        public DbSet<EmployeePerformance> EmployeePerformances { get; set; }

        // Test table
        public DbSet<TestEntity> TestEntities { get; set; }

        protected override void OnModelCreating(ModelBuilder modelBuilder)
        {
            base.OnModelCreating(modelBuilder);

            // SalesReport configuration
            modelBuilder.Entity<SalesReport>(entity =>
            {
                entity.HasKey(e => e.Id);
                entity.Property(e => e.TotalRevenue).HasColumnType("decimal(18,2)");
                entity.Property(e => e.TotalSales).HasColumnType("decimal(18,2)");
                entity.Property(e => e.TotalRentals).HasColumnType("decimal(18,2)");
                entity.Property(e => e.AverageOrderValue).HasColumnType("decimal(18,2)");
                entity.Property(e => e.GrowthRate).HasColumnType("decimal(5,2)");
                entity.Property(e => e.Period).HasMaxLength(20);
                entity.Property(e => e.TopSellingCategory).HasMaxLength(100);
                entity.Property(e => e.GeneratedBy).HasMaxLength(100);
                entity.HasIndex(e => e.ReportDate);
            });

            // ProductPerformance configuration
            modelBuilder.Entity<ProductPerformance>(entity =>
            {
                entity.HasKey(e => e.Id);
                entity.Property(e => e.TotalRevenue).HasColumnType("decimal(18,2)");
                entity.Property(e => e.ProfitMargin).HasColumnType("decimal(5,2)");
                entity.Property(e => e.ProductName).HasMaxLength(200);
                entity.Property(e => e.SKU).HasMaxLength(50);
                entity.Property(e => e.PerformanceRating).HasMaxLength(20);
                entity.HasIndex(e => e.ProductId);
            });

            // EmployeePerformance configuration
            modelBuilder.Entity<EmployeePerformance>(entity =>
            {
                entity.HasKey(e => e.Id);
                entity.Property(e => e.TotalRevenue).HasColumnType("decimal(18,2)");
                entity.Property(e => e.EmployeeName).HasMaxLength(200);
                entity.Property(e => e.Position).HasMaxLength(100);
                entity.Property(e => e.PerformanceGrade).HasMaxLength(2);
                entity.HasIndex(e => e.EmployeeId);
            });

            // TestEntity configuration
            modelBuilder.Entity<TestEntity>(entity =>
            {
                entity.HasKey(e => e.Id);
                entity.Property(e => e.Name).HasMaxLength(200).IsRequired();
                entity.Property(e => e.CreatedAt).HasDefaultValueSql("GETUTCDATE()");
            });
        }
    }

    // Test entity
    public class TestEntity
    {
        public int Id { get; set; }
        public string Name { get; set; } = string.Empty;
        public DateTime CreatedAt { get; set; }
    }
}