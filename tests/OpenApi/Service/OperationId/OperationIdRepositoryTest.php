<?php

namespace Ouzo\OpenApi\Service\OperationId;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class OperationIdRepositoryTest extends TestCase
{
    #[Test]
    public function shouldGetLastOperationId(): void
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

    #[Test]
    public function shouldReturnFalseIfOperationIdIsNotInRepository(): void
    {
        //given
        $operationIdRepository = new OperationIdRepository();

        //when
        $hasOperationId = $operationIdRepository->hasOperationId('not exists');

        //then
        $this->assertFalse($hasOperationId);
    }

    #[Test]
    public function shouldReturnTrueIfOperationIdIsInRepository(): void
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
