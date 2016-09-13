<?php namespace dataAdapter\newsletter;

require_once(__DIR__.'/INewsletterDataAdapter.php');
require_once(__DIR__.'/../../exceptions/MailAddressAlreadyRegisteredException.php');
require_once(__DIR__.'/../../exceptions/DatabaseException.php');

use exceptions\MailAddressAlreadyRegisteredException;
use exceptions\DatabaseException;

/**
* The INewsletterDataAdapter implemenation for a MySQL Database.
*/
class MySQLNewsletterDataAdapter implements INewsletterDataAdapter
{

    /**
    * Holds the PDO instance.
    */
    private $pdo = null;

    /**
    * Creates a new instance of the MySQLNewsletterDataAdapter class.
    *
    * @param dbConnection The PDO database connection string. 
    * @param dbUser The database user. 
    * @param dbPassword The database password. 
    *
    * @throws InvalidArgumentException Is thrown if an argument is null.
    * @throws DatabaseException Is thrown if an error occurred while connecting to the database.
    */
    public function __construct($dbConnection, $dbUser, $dbPassword)
	{
        if(is_null($dbConnection))
        {
            throw new \InvalidArgumentException("The parameter dbConnection can't be null.");
        }

        if(is_null($dbUser))
        {
            throw new \InvalidArgumentException("The parameter dbUser can't be null.");
        }

        if(is_null($dbPassword))
        {
            throw new \InvalidArgumentException("The parameter dbPassword can't be null.");
        }

        try
        {
            $this->pdo = new \PDO($dbConnection, $dbUser, $dbPassword,  array(\PDO::ATTR_EMULATE_PREPARES => false, \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION, \PDO::ATTR_TIMEOUT => 1));
        }
        catch(\PDOException $ex)
        {
            throw new DatabaseException("There was an unexpected database error.", 0, $ex);
        }
    }

    /**
    * Inserts the mail address and the name to the data.
    *
    * @param string $name 
    *  The name.
    *
    * @param string $mail
    *  The mail address.    
    *
    * @throws DatabaseException Is thrown if something with the database went wrong.
    * @throws MailAddressAlreadyRegisteredException Is thrown if the given mail address is already registered.   
    */
    public function insertMailAddress($name, $mail)
    {
        $stmt = $this->pdo->prepare("INSERT INTO newsletteraddressee(addresseeName, addresseeMailAddress) VALUES(:name, :mail);");
        
        try
        {
            $stmt->execute(array(':name' => $name, ':mail' => $mail));
        }
        catch(\PDOException $ex)
        {
            if($ex->errorInfo[1] == 1062)
            {
                throw new MailAddressAlreadyRegisteredException("The mail address is already registeres.");
            }

            throw new DatabaseException("There was an unexpected database error.", $ex);
        }

     }

}

?>