<?php
/**
 * Created by PhpStorm.
 * User: webanet
 * Date: 04.12.2025
 * Time: 17:12
 */

namespace application\repositories;


class UserRepository
{

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function findById( int $id ) : ?\application\models\User
    {
        $row = \application\core\DB::i()->query( "SELECT * FROM `users` WHERE `id` = ?i LIMIT 1", $id )->fetchAssoc();

        return $row ? new \application\models\User( $row ) : null;
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function findByEmail( string $email ) : ?\application\models\User
    {
        $row = \application\core\DB::i()->query( "SELECT * FROM `users` WHERE `email` = '?s' LIMIT 1", $email )->fetchAssoc();

        return $row ? new \application\models\User( $row ) : null;
    }

    /**
     * @throws \Krugozor\Database\MySqlException
     */
    public function save( \application\models\User $user ) : void
    {
        if ( $user->id > 0 )
        {
            \application\core\DB::i()->query( "UPDATE `users` SET `email`= '?s', `password_hash` = '?s', `name` = '?s', `role` = '?s' WHERE id = ?i", $user->email, $user->password_hash, $user->name, $user->role, $user->id );
        }
        else
        {
            \application\core\DB::i()->query( "INSERT INTO users (`email`, `password_hash`, `name`, `role`) VALUES ('?s', '?s', '?s', '?s')", $user->email, $user->password_hash, $user->name, $user->role );
            $user->id = \application\core\DB::i()->getLastInsertId();
        }
    }

}
