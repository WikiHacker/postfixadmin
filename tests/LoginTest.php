<?php

class LoginTest extends \PHPUnit\Framework\TestCase {
    public function setUp(): void {
        $this->cleanUp();

        db_execute("INSERT INTO domain(`domain`, description, transport) values ('example.com', 'test', 'foo')", [], true);

        db_execute(
            "INSERT INTO mailbox(username, password, `name`, maildir, local_part, `domain`) 
VALUES(:username, :password, :name, :maildir, :local_part, :domain)",
            [
                'username' => 'test@example.com',
                'password' => pacrypt('foobar'),
                'name' => 'test user',
                'maildir' => '/foo/bar',
                'local_part' => 'test',
                'domain' => 'example.com',
            ]);
        parent::setUp();
    }


    public function tearDown(): void {
        $this->cleanUp();
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    private function cleanUp() {
        db_query('DELETE FROM mailbox');
        db_query('DELETE FROM domain');
    }

    public function testInvalidUsers() {
        $login = new Login('mailbox', 'username');

        $this->assertFalse($login->login('test', 'password'));
        $this->assertFalse($login->login('test', ''));
        $this->assertFalse($login->login('', ''));
    }


    public function testValidLogin() {
        $login = new Login('mailbox', 'username');

        $this->assertFalse($login->login('test', 'password'));
        $this->assertFalse($login->login('test', 'foobar'));
        $this->assertFalse($login->login('', ''));
    }

    public function testPasswordRecovery() {
        $login = new Login('mailbox', 'username');
        $this->assertFalse($login->generatePasswordRecoveryCode(''));
        $this->assertFalse($login->generatePasswordRecoveryCode('doesnotexist'));
        $this->assertNotEmpty($login->generatePasswordRecoveryCode('test@example.com'));
    }
}