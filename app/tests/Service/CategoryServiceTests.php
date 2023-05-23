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
        $categoryRepositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paginatorMock = $this->getMockBuilder(PaginatorInterface::class)
            ->getMock();

        $categoryService = new CategoryService($categoryRepositoryMock, $paginatorMock);


        return true;
    }

    public function testCreatePaginatedList(): void
    {
        $categoryRepositoryMock = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $paginatorMock = $this->getMockBuilder(PaginatorInterface::class)
            ->getMock();

        $categoryService = new CategoryService($categoryRepositoryMock, $paginatorMock);

        $queryBuilderMock = $this->getMockBuilder(\Doctrine\ORM\QueryBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $categoryRepositoryMock->expects($this->once())
            ->method('queryAll')
            ->willReturn($queryBuilderMock);

        $paginationMock = $this->getMockBuilder(PaginationInterface::class)
            ->getMock();

        $paginatorMock->expects($this->once())
            ->method('paginate')
            ->with($queryBuilderMock, 1, CategoryRepository::PAGINATOR_ITEMS_PER_PAGE)
            ->willReturn($paginationMock);

        $result = $categoryService->createPaginatedList(1);

  
        $this->assertInstanceOf(PaginationInterface::class, $result);
  
    }
}
