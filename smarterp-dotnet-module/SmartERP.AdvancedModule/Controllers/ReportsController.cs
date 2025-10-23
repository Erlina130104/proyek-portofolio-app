using Microsoft.AspNetCore.Mvc;
using SmartERP.AdvancedModule.DTOs;
using SmartERP.AdvancedModule.Services;

namespace SmartERP.AdvancedModule.Controllers
{
    [ApiController]
    [Route("api/[controller]")]
    [Produces("application/json")]
    public class ReportsController : ControllerBase
    {
        private readonly IReportService _reportService;
        private readonly ILogger<ReportsController> _logger;

        public ReportsController(IReportService reportService, ILogger<ReportsController> logger)
        {
            _reportService = reportService;
            _logger = logger;
        }

        /// <summary>
        /// Generate comprehensive sales report
        /// </summary>
        /// <param name="request">Report generation parameters</param>
        /// <returns>Sales report with analytics</returns>
        [HttpPost("sales")]
        [ProducesResponseType(typeof(ApiResponse<SalesReportDto>), 200)]
        public async Task<ActionResult<ApiResponse<SalesReportDto>>> GenerateSalesReport([FromBody] GenerateReportRequest request)
        {
            try
            {
                _logger.LogInformation("Generating sales report for period: {Start} to {End}", request.StartDate, request.EndDate);

                var report = await _reportService.GenerateSalesReportAsync(
                    request.StartDate, 
                    request.EndDate, 
                    request.GroupBy);

                return Ok(new ApiResponse<SalesReportDto>
                {
                    Success = true,
                    Message = "Sales report generated successfully",
                    Data = report
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error generating sales report");
                return StatusCode(500, new ApiResponse<SalesReportDto>
                {
                    Success = false,
                    Message = "An error occurred while generating the report"
                });
            }
        }

        /// <summary>
        /// Get product performance analytics
        /// </summary>
        [HttpGet("products/performance")]
        [ProducesResponseType(typeof(ApiResponse<List<ProductPerformanceDto>>), 200)]
        public async Task<ActionResult<ApiResponse<List<ProductPerformanceDto>>>> GetProductPerformance(
            [FromQuery] DateTime startDate, 
            [FromQuery] DateTime endDate)
        {
            try
            {
                var performance = await _reportService.GetProductPerformanceAsync(startDate, endDate);

                return Ok(new ApiResponse<List<ProductPerformanceDto>>
                {
                    Success = true,
                    Message = $"Retrieved performance data for {performance.Count} products",
                    Data = performance
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving product performance");
                return StatusCode(500, new ApiResponse<List<ProductPerformanceDto>>
                {
                    Success = false,
                    Message = "An error occurred while retrieving product performance"
                });
            }
        }

        /// <summary>
        /// Get dashboard summary metrics
        /// </summary>
        [HttpGet("dashboard/summary")]
        [ProducesResponseType(typeof(ApiResponse<DashboardSummaryDto>), 200)]
        public async Task<ActionResult<ApiResponse<DashboardSummaryDto>>> GetDashboardSummary()
        {
            try
            {
                var summary = await _reportService.GetDashboardSummaryAsync();

                return Ok(new ApiResponse<DashboardSummaryDto>
                {
                    Success = true,
                    Message = "Dashboard summary retrieved successfully",
                    Data = summary
                });
            }
            catch (Exception ex)
            {
                _logger.LogError(ex, "Error retrieving dashboard summary");
                return StatusCode(500, new ApiResponse<DashboardSummaryDto>
                {
                    Success = false,
                    Message = "An error occurred while retrieving dashboard summary"
                });
            }
        }
    }
}