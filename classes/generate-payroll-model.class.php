<?php
if (!isset($_SESSION)) {
    session_start();
}
require_once "../config.php";

// loop through the shipmentNumberArray and create one billedShipment entry on each pass (Do this on model, just pass the shipmentNumberArray onto model)

class GenerateInvoiceModel
{
    private $typeAdd;
    private $invoiceNumberAdd;
    private $vehicleAdd;
    private $companyId;


    public function __construct(
        $typeAdd,
        $invoiceNumberAdd,
        $vehicleAdd,
        $companyId
    ) {

        $this->typeAdd = $typeAdd;
        $this->invoiceNumberAdd = $invoiceNumberAdd;
        $this->vehicleAdd = $vehicleAdd;
        $this->companyId = $companyId;
    }

    public function generateInvoiceRecord()
    {
        //$this->getVehicleFees($this->vehicleAdd);
        //$this->getPayrollShipment($this->vehicleAdd);

        if ($this->typeAdd == 'batch') {
            $this->submitBatch();
        }

        if ($this->typeAdd == 'single') {
            if ($this->vehicleValidator($this->invoiceNumberAdd, $this->vehicleAdd)) {
                $this->getVehicleFees($this->vehicleAdd);
                $this->getPayrollShipment($this->vehicleAdd);
            }
        }

        /*
        $this->getVehicleFees();

        for ($i = 0; $i < count($this->shipmentNumberArray); $i++) {

            $this->billedShipmentSubmit($this->shipmentNumberArray[$i]);
        }

        $this->updateInvoiceSubmit();
*/
    }

    public function vehicleValidator($var1, $var2)
    {
        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        $sql = "SELECT
        payroll.plate_number, payroll.billing_id
        FROM payroll
        WHERE payroll.billing_id = :billing_id 
        AND payroll.plate_number = :plate_number";

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":billing_id", $param1, PDO::PARAM_STR);
            $stmt->bindParam(":plate_number", $param2, PDO::PARAM_STR);

            $param1 = $var1;
            $param2 = $var2;

            if ($stmt->execute()) {
                $row = $stmt->fetchAll();

                if (count($row) > 0) {
                    $returnValue = false;
                } else {
                    $returnValue = true;
                }
            } else {
            }

            unset($stmt);

            return $returnValue;
        }
        unset($pdoVessel);
    }

    public function submitBatch()
    {

        $sql = "SELECT billedshipment.billing_id, shipment.plate_number
        FROM billedshipment
        INNER JOIN shipment
        ON billedshipment.shipment_id = shipment.shipment_id
        WHERE billedshipment.billing_id = :billing_id GROUP BY shipment.plate_number";

        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":billing_id", $param1, PDO::PARAM_STR);

            $param1 = $this->invoiceNumberAdd;

            if ($stmt->execute()) {
                $row = $stmt->fetchAll();

                for ($i = 0; $i < count($row); $i++) {
                    //echo $row[$i][1] . "<br>";
                    if ($this->vehicleValidator($row[$i][0], $row[$i][1])) {
                        $this->getVehicleFees($row[$i][1]);
                        $this->getPayrollShipment($row[$i][1]);
                    }
                }
            } else {

                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../modal-prompt.php');
                exit();
            }

            unset($stmt);
        }
        unset($pdoVessel);
    }

    public function getVehicleFees($plateNumber)
    {

        $sql = "SELECT SUM(shipment.area_rate), SUM(shipmentfees.drop_fee), SUM(shipmentfees.penalty)
        FROM billedshipment
        INNER JOIN shipment
        ON billedshipment.shipment_id = shipment.shipment_id
        INNER JOIN shipmentfees
        ON shipment.shipment_id = shipmentfees.shipment_id
        WHERE shipment.plate_number = :plate_number AND billedshipment.billing_id = :billing_id";

        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":plate_number", $param1, PDO::PARAM_STR);
            $stmt->bindParam(":billing_id", $param2, PDO::PARAM_STR);

            $param1 = $plateNumber;
            $param2 = $this->invoiceNumberAdd;

            if ($stmt->execute()) {
                $row = $stmt->fetchAll();
                $this->generatePayrollSubmit($plateNumber, $row[0][0], $row[0][1], $row[0][2]);
                //echo "Successfully added a record!";
            } else {

                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../modal-prompt.php');
                exit();
            }

            unset($stmt);
        }
        unset($pdoVessel);
    }

    public function getPayrollShipment($plateNumber)
    {

        $sql = "SELECT shipment.shipment_id
        FROM billedshipment
        INNER JOIN shipment
        ON billedshipment.shipment_id = shipment.shipment_id
        INNER JOIN shipmentfees
        ON shipment.shipment_id = shipmentfees.shipment_id
        WHERE shipment.plate_number = :plate_number AND billedshipment.billing_id = :billing_id";

        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":plate_number", $param1, PDO::PARAM_STR);
            $stmt->bindParam(":billing_id", $param2, PDO::PARAM_STR);

            $param1 = $plateNumber;
            $param2 = $this->invoiceNumberAdd;

            if ($stmt->execute()) {
                $row = $stmt->fetchAll();

                for ($i = 0; $i < count($row); $i++) {

                    $this->payrollShipmentSubmit($row[$i][0], $this->getPayrollId($plateNumber));
                }

                //$this->generatePayrollSubmit($plateNumber, $row[0][0], $row[0][1], $row[0][2]);
                //echo "Successfully added a record!";
            } else {

                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../modal-prompt.php');
                exit();
            }

            unset($stmt);
        }
        unset($pdoVessel);
    }

    public function generatePayrollSubmit($var1, $var2, $var3, $var4)
    {

        $sql = "INSERT INTO 
                payroll(
                payroll_status, 
                billing_id,
                plate_number,
                truck_rate,
                drop_off,
                penalty,
                company_id
                ) 
                VALUES( 
                :payroll_status, 
                :billing_id,
                :plate_number,
                :truck_rate,
                :drop_off,
                :penalty,
                :company_id
                )";

        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":payroll_status", $param1, PDO::PARAM_STR);
            $stmt->bindParam(":billing_id", $param2, PDO::PARAM_STR);
            $stmt->bindParam(":plate_number", $param3, PDO::PARAM_STR);
            $stmt->bindParam(":truck_rate", $param4, PDO::PARAM_STR);
            $stmt->bindParam(":drop_off", $param5, PDO::PARAM_STR);
            $stmt->bindParam(":penalty", $param6, PDO::PARAM_STR);
            $stmt->bindParam(":company_id", $param7, PDO::PARAM_STR);

            $param1 = 'Unsettled';
            $param2 = $this->invoiceNumberAdd;
            $param3 = $var1;
            $param4 = $var2;
            $param5 = $var3;
            $param6 = $var4;
            $param7 = $this->companyId;

            if ($stmt->execute()) {
                //echo "Successfully added a record!";
            } else {

                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../modal-prompt.php');
                exit();
            }


            unset($stmt);
        }
        unset($pdoVessel);
    }

    public function payrollShipmentSubmit($shipmentNumberVar, $payrollIdVar)
    {

        $sql = "INSERT INTO 
                payrollshipment(
                shipment_id,
                payroll_id
                ) 
                VALUES(
                :shipment_id,
                :payroll_id
                )";

        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":shipment_id", $param1, PDO::PARAM_STR);
            $stmt->bindParam(":payroll_id", $param2, PDO::PARAM_STR);

            $param1 = $shipmentNumberVar;
            $param2 = $payrollIdVar;

            if ($stmt->execute()) {
                //echo "Successfully added a record!";
            } else {

                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../modal-prompt.php');
                exit();
            }

            unset($stmt);
        }
        unset($pdoVessel);
    }

    public function getPayrollId($plateNumber)
    {
        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        $sql = "SELECT 
        payroll_id 
        FROM payroll 
        WHERE billing_id = :billing_id AND plate_number = :plate_number";

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":billing_id", $param1, PDO::PARAM_STR);
            $stmt->bindParam(":plate_number", $param2, PDO::PARAM_STR);


            $param1 = $this->invoiceNumberAdd;
            $param2 = $plateNumber;

            if ($stmt->execute()) {
                $row = $stmt->fetchAll();
                $returnValue = $row[0][0];
            } else {
                session_start();
                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../prompt.php');
                exit();
            }

            unset($stmt);

            return $returnValue;
        }
        unset($pdoVessel);
    }

    /*
    public function generateInvoiceSubmit()
    {

        $sql = "INSERT INTO 
                billing(
                invoice_number, 
                invoice_date,
                covered_date,
                due_date,
                billing_status,
                client_id,
                truck_cost,
                drop_fee,
                parking_fee,
                toll_fee,
                fuel_charge,
                extra_helper,
                demurrage,
                other_charges,
                less_penalties
                ) 
                VALUES( 
                :invoice_number, 
                :invoice_date,
                :covered_date,
                :due_date,
                :billing_status,
                :client_id,
                :truck_cost,
                :drop_fee,
                :parking_fee,
                :toll_fee,
                :fuel_charge,
                :extra_helper,
                :demurrage,
                :other_charges,
                :less_penalties
                )";

        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":invoice_number", $param1, PDO::PARAM_STR);
            $stmt->bindParam(":invoice_date", $param2, PDO::PARAM_STR);
            $stmt->bindParam(":covered_date", $param3, PDO::PARAM_STR);
            $stmt->bindParam(":due_date", $param4, PDO::PARAM_STR);
            $stmt->bindParam(":billing_status", $param5, PDO::PARAM_STR);
            $stmt->bindParam(":client_id", $param6, PDO::PARAM_STR);
            $stmt->bindParam(":truck_cost", $param7, PDO::PARAM_STR);
            $stmt->bindParam(":drop_fee", $param8, PDO::PARAM_STR);
            $stmt->bindParam(":parking_fee", $param9, PDO::PARAM_STR);
            $stmt->bindParam(":toll_fee", $param10, PDO::PARAM_STR);
            $stmt->bindParam(":fuel_charge", $param11, PDO::PARAM_STR);
            $stmt->bindParam(":extra_helper", $param12, PDO::PARAM_STR);
            $stmt->bindParam(":demurrage", $param13, PDO::PARAM_STR);
            $stmt->bindParam(":other_charges", $param14, PDO::PARAM_STR);
            $stmt->bindParam(":less_penalties", $param15, PDO::PARAM_STR);

            $param1 = $this->invoiceNumberAdd;
            $param2 = $this->invoiceDateAdd;
            $param3 = 'na';

            $param5 = 'Unsettled';
            $param6 = $this->clientAdd;
            $param7 = 0;
            $param8 = 0;
            $param9 = 0;
            $param10 = 0;
            $param11 = 0;
            $param12 = 0;
            $param13 = 0;
            $param14 = 0;
            $param15 = 0;

            $date = date_create($this->invoiceDateAdd);

            date_add($date, date_interval_create_from_date_string("30 days"));
            //echo date_format($date, "Y-m-d") . "<br>";

            $param4 = date_format($date, "Y-m-d");

            if ($stmt->execute()) {
                //echo "Successfully added a record!";
            } else {

                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../modal-prompt.php');
                exit();
            }


            unset($stmt);
        }
        unset($pdoVessel);
    }

    public function updateInvoiceSubmit()
    {

        $array1 = $this->getInvoiceFees();
        $start = $this->getStartDate();
        $end = $this->getEndDate();
        $sql = "UPDATE 
                billing
                SET
                covered_date = :covered_date,
                truck_cost = :truck_cost,
                drop_fee = :drop_fee,
                parking_fee = :parking_fee,
                toll_fee = :toll_fee,
                fuel_charge = :fuel_charge,
                extra_helper = :extra_helper,
                demurrage = :demurrage,
                other_charges = :other_charges,
                less_penalties = :less_penalties
                WHERE billing_id = :billing_id";

        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        if ($stmt = $pdoVessel->prepare($sql)) {


            $stmt->bindParam(":covered_date", $param3, PDO::PARAM_STR);
            $stmt->bindParam(":truck_cost", $param7, PDO::PARAM_STR);
            $stmt->bindParam(":drop_fee", $param8, PDO::PARAM_STR);
            $stmt->bindParam(":parking_fee", $param9, PDO::PARAM_STR);
            $stmt->bindParam(":toll_fee", $param10, PDO::PARAM_STR);
            $stmt->bindParam(":fuel_charge", $param11, PDO::PARAM_STR);
            $stmt->bindParam(":extra_helper", $param12, PDO::PARAM_STR);
            $stmt->bindParam(":demurrage", $param13, PDO::PARAM_STR);
            $stmt->bindParam(":other_charges", $param14, PDO::PARAM_STR);
            $stmt->bindParam(":less_penalties", $param15, PDO::PARAM_STR);
            $stmt->bindParam(":billing_id", $param16, PDO::PARAM_STR);

            $param3 = $start . ' - ' . $end;
            $param7 = $array1[0][0];
            $param8 = $array1[0][1];
            $param9 = $array1[0][2];
            $param10 = $array1[0][3];
            $param11 = $array1[0][4];
            $param12 = $array1[0][5];
            $param13 = $array1[0][6];
            $param14 = $array1[0][7];
            $param15 = $array1[0][8];
            $param16 = $this->getBillingId();

            if ($stmt->execute()) {
                //echo "Successfully added a record!";
            } else {

                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../modal-prompt.php');
                exit();
            }


            unset($stmt);
        }
        unset($pdoVessel);
    }

    public function billedShipmentSubmit($shipmentNumberVar)
    {

        $sql = "INSERT INTO 
                billedshipment(
                shipment_id,
                billing_id
                ) 
                VALUES(
                :shipment_id,
                :billing_id
                )";

        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":shipment_id", $param1, PDO::PARAM_STR);
            $stmt->bindParam(":billing_id", $param2, PDO::PARAM_STR);

            $param1 = $shipmentNumberVar;
            $param2 = $this->getBillingId();

            if ($stmt->execute()) {
                //echo "Successfully added a record!";
            } else {

                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../modal-prompt.php');
                exit();
            }

            unset($stmt);
        }
        unset($pdoVessel);
    }

    public function getBillingId()
    {
        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        $sql = "SELECT 
        billing_id 
        FROM billing 
        WHERE invoice_number = :invoice_number AND invoice_date = :invoice_date";

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":invoice_number", $param1, PDO::PARAM_STR);
            $stmt->bindParam(":invoice_date", $param2, PDO::PARAM_STR);


            $param1 = $this->invoiceNumberAdd;
            $param2 = $this->invoiceDateAdd;

            if ($stmt->execute()) {
                $row = $stmt->fetchAll();
                $returnValue = $row[0][0];
            } else {
                session_start();
                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../prompt.php');
                exit();
            }

            unset($stmt);

            return $returnValue;
        }
        unset($pdoVessel);
    }

    public function getInvoiceFees()
    {

        $sql = "SELECT 
        SUM(shipment.area_rate), 
        SUM(shipmentfees.drop_fee), 
        SUM(shipmentfees.parking_fee),
        SUM(shipmentfees.toll_fee),
        SUM(shipmentfees.fuel_charge),
        SUM(shipmentfees.extra_helper),
        SUM(shipmentfees.demurrage),
        SUM(shipmentfees.other_charges),
        SUM(shipmentfees.penalty)
        FROM shipment
        INNER JOIN shipmentfees
        ON shipment.shipment_id = shipmentfees.shipment_id
        INNER JOIN billedshipment
        ON shipment.shipment_id = billedshipment.shipment_id
        WHERE billedshipment.billing_id = :billing_id";

        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":billing_id", $param2, PDO::PARAM_STR);

            $param2 = $this->getBillingId();

            if ($stmt->execute()) {
                $row = $stmt->fetchAll();
                $returnValue = $row;
            } else {
                session_start();
                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../prompt.php');
                exit();
            }

            unset($stmt);

            return $returnValue;
        }
        unset($pdoVessel);
    }

    public function getStartDate()
    {
        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        $sql = "SELECT 
        shipment.date_of_delivery
        FROM shipment
        INNER JOIN billedshipment
        ON shipment.shipment_id = billedshipment.shipment_id
        WHERE billedshipment.billing_id = :billing_id
        ORDER BY shipment.date_of_delivery ASC";

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":billing_id", $param2, PDO::PARAM_STR);

            $param2 = $this->getBillingId();

            if ($stmt->execute()) {
                $row = $stmt->fetchAll();
                $returnValue = $row[0][0];
            } else {
                session_start();
                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../prompt.php');
                exit();
            }

            unset($stmt);

            return $returnValue;
        }
        unset($pdoVessel);
    }

    public function getEndDate()
    {
        $configObj = new Config();

        $pdoVessel = $configObj->pdoConnect();

        $sql = "SELECT 
        shipment.date_of_delivery
        FROM shipment
        INNER JOIN billedshipment
        ON shipment.shipment_id = billedshipment.shipment_id
        WHERE billedshipment.billing_id = :billing_id
        ORDER BY shipment.date_of_delivery DESC";

        if ($stmt = $pdoVessel->prepare($sql)) {

            $stmt->bindParam(":billing_id", $param2, PDO::PARAM_STR);

            $param2 = $this->getBillingId();

            if ($stmt->execute()) {
                $row = $stmt->fetchAll();
                $returnValue = $row[0][0];
            } else {
                session_start();
                $_SESSION["prompt"] = "Something went wrong!";
                header('location: ../prompt.php');
                exit();
            }

            unset($stmt);

            return $returnValue;
        }
        unset($pdoVessel);
    }
    */
}