<?php
namespace Tiny\Sql\StatementsSplitterFSM;

interface EventsInterface {
    public function token($token);
    public function comment($comment);
    public function semicolon();
    public function done();
}
