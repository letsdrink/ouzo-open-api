<?php

namespace Ouzo\OpenApi\Service\OperationId;

use PHPUnit\Framework\TestCase;

class OperationIdRepositoryTest extends TestCase
{
    /**
     * @test
     */
    public function shouldGetLastOperationId()
    {
        //given
        $operationIdRepository = new OperationIdRepository();
        $operationIdRepository->add('operation');
        $operationIdRepository->add('something else');
        $operationIdRepository->add('operation_2');
        $operationIdRepository->add('awesome');
        $operationIdRepository->add('operation_3');

        //when
        $operationId = $operationIdRepository->getLastOperationId('operation');

        //then
        $this->assertSame('operation_3', $operationId);
    }

    /**
     * @test
     */
    public function shouldReturnFalseIfOperationIdIsNotInRepository()
    {
        //given
        $operationIdRepository = new OperationIdRepository();

        //when
        $hasOperationId = $operationIdRepository->hasOperationId('not exists');

        //then
        $this->assertFalse($hasOperationId);
    }

    /**
     * @test
     */
    public function shouldReturnTrueIfOperationIdIsInRepository()
    {
        //given
        $operationIdRepository = new OperationIdRepository();
        $operationIdRepository->add('operation');

        //when
        $hasOperationId = $operationIdRepository->hasOperationId('operation');

        //then
        $this->assertTrue($hasOperationId);
    }
}
