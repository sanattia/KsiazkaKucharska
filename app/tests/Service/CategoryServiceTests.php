<?php
/**
 * Category service tests.
 */

namespace App\tests\Service;

use App\Service\CategoryService;
use Generator;
use Knp\Component\Pager\Pagination\PaginationInterface;
use PHPUnit\Framework\TestCase;
use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;

/**
 * Class FizzBuzzServiceTest.
 */
class CategoryServiceTests extends TestCase
{


    public function testConstructor(): bool
    {
        // Create mock objects for the dependencies
        $categoryRepositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paginatorMock = $this->getMockBuilder(PaginatorInterface::class)
            ->getMock();

        // Create an instance of the service with the mocked dependencies
        $categoryService = new CategoryService($categoryRepositoryMock, $paginatorMock);

        // Additional assertions if necessary

        return true;
    }

    public function testCreatePaginatedList(): void
    {
        // Create mock objects for the dependencies
        $categoryRepositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paginatorMock = $this->getMockBuilder(PaginatorInterface::class)
            ->getMock();

        // Create an instance of the service with the mocked dependencies
        $categoryService = new CategoryService($categoryRepositoryMock, $paginatorMock);

        // Mock the queryAll() method of the category repository
        $queryBuilderMock = $this->getMockBuilder(\Doctrine\ORM\QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $categoryRepositoryMock->expects($this->once())
            ->method('queryAll')
            ->willReturn($queryBuilderMock);

        // Mock the paginate() method of the paginator
        $paginationMock = $this->getMockBuilder(PaginationInterface::class)
            ->getMock();

        $paginatorMock->expects($this->once())
            ->method('paginate')
            ->with($queryBuilderMock, 1, CategoryRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($paginationMock);

        // Call the method being tested
        $result = $categoryService->createPaginatedList(1);

        // Assert the result
        $this->assertInstanceOf(PaginationInterface::class, $result);
        // Additional assertions if necessary
    }
}