<?php

namespace OCA\CustomProperties\Controller;

use OCA\CustomProperties\AppInfo\Application;
use OCA\CustomProperties\Db\CustomPropertiesMapper;
use OCA\CustomProperties\Db\CustomProperty;
use OCA\CustomProperties\Error\CustomPropertyInvalidError;
use OCP\IRequest;
use PHPUnit\Framework\TestCase;

class CustomPropertiesControllerTest extends TestCase
{
    private CustomPropertiesController $controller;
    /**
     * @var CustomPropertiesMapper|\PHPUnit\Framework\MockObject\MockObject
     */
    private $customPropertiesMapper;

    protected function setUp(): void
    {
        parent::setUp();

        $this->customPropertiesMapper = $this->createMock(CustomPropertiesMapper::class);

        $this->controller = new CustomPropertiesController(
            Application::APP_ID,
            $this->createMock(IRequest::class),
            $this->customPropertiesMapper
        );

    }

    /**
     * @dataProvider invalidCustomPropertyProvider
     */
    public function testCreateWithInvalidCustomPropertyThrowsError($propertyname, $propertytype)
    {
        $this->expectExceptionObject(new CustomPropertyInvalidError());

        $customProperty = new CustomProperty();
        $customProperty->setPropertyname($propertyname);
        $customProperty->setPropertytype($propertytype);

        $this->controller->create($customProperty);
    }

    public function testCreateWithValidCustomPropertyIsStored()
    {
        $this->customPropertiesMapper->expects(self::once())
            ->method('insert');

        $customProperty = new CustomProperty();
        $customProperty->setPropertyname("propertyname");
        $customProperty->setPropertylabel("I Am A Label");
        $customProperty->setPropertytype("text");

        $this->controller->create($customProperty);
    }

    public function testUpdateWithValidCustomPropertyIsUpdated()
    {
        $existingProperty = new CustomProperty();
        $existingProperty->setId(1);
        $existingProperty->setPropertytype("text");

        $this->customPropertiesMapper->method('findById')
            ->with(1)
            ->willReturn($existingProperty);

        $this->customPropertiesMapper->expects(self::once())
            ->method('update')
            ->willReturnArgument(0);

        $changedProperty = new CustomProperty();
        $changedProperty->setId(1);
        $changedProperty->setPropertyname("propertyname");
        $changedProperty->setPropertylabel("I Am A Label");
        $changedProperty->setPropertytype("date");

        $actual = $this->controller->update($changedProperty);
        $this->assertEquals("propertyname", $actual->propertyname);
        $this->assertEquals("I Am A Label", $actual->propertylabel);
        $this->assertEquals("text", $actual->propertytype);
    }

    public function invalidCustomPropertyProvider()
    {
        return array(
            array(null, "text"),
            array("no spaces allowed", "text"),
            array("UpperCase", "text"),
            array("9startingwithnumber", "text"),
            array("_", "text"),
            array("-", "text"),
            array(":", "text"),
            array("👍", "text"),
            array("       ", "text"),
        );
    }
}
