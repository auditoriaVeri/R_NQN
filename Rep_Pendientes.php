<?php

/***************************************************************************
 *
 *				PENDIENTES
 *
 **************************************************************************/
if($continua)
{
  $sql = "SELECT * FROM pendientes WHERE Replicado = 0 AND idTaller = $idTaller";

  $respPendientes = $base->query($sql);

  if ($respPendientes)
  {
    /***** Por cada equipo vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respPendientes as $RPendiente)
    {
      $ComandoSQL =
        " SELECT COUNT(*) as HM  
        FROM pendientes
        WHERE
          idTaller = :idTaller AND 
          idPendiente = :idPendiente";

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idPendiente', $RPendiente["idPendiente"]);
      $SSQL->bindValue(':idTaller', $RPendiente["idTaller"]);

      $Res= $SSQL->execute();  // Para ver si anduvo pero no le doy bola
      $R = $SSQL->fetch();

      $HM = $R["HM"];

      if ($HM == 0)
      {
        $ComandoSQL =
          "INSERT INTO pendientes
           (
            idPendiente, Fecha, Hora, HoraFinal, idTaller, idFotovalidacion, idVerificacion, DominioVehiculo, idHabilitacion, codigoHabilitacion, ChasisNro, MotorAnio, MotorMarca, MotorNumero, idLocalidadVehiculo, TipoDocConductor, NroDocConductor, NombreConductor, ApellidoConductor, Reverificacion, idVerificacionOriginal, Inspector, DirectorTecnico, idEstado, Eje1_Tara, Eje2_Tara, Eje3_Tara, Eje4_Tara, Eje1_FzaIzq, Eje2_FzaIzq, Eje3_FzaIzq, Eje4_FzaIzq, Eje1_FzaDer, Eje2_FzaDer, Eje3_FzaDer, Eje4_FzaDer, Eje1_Dif, Eje2_Dif, Eje3_Dif, Eje4_Dif, Eje1_Eficiencia, Eje2_Eficiencia, Eje3_Eficiencia, Eje4_Eficiencia, Eje5_Tara, Eje5_FzaIzq, Eje5_FzaDer, Eje5_Dif, Eje5_Eficiencia, Alineacion, NivelSonoro, Interior, Escape, Bach, PorcentajeCo, Opac, ppmHC, Freno_FzaIsq, Freno_FzaDer, Freno_Dif, Freno_Eficiencia, CCCF, MarcaTac, NroTac, RodadoTac, NroInterno, CodigoTitular, DescripcionTitular, Susp_FzaIsq, Susp_FzaDer, Susp_Dif, Susp_Eficiencia, Observaciones, CompaniaSeguro, NroPoliza, UltimoRecPatente, idTipoUso, usuarioCarga, idTipoVehiculo, VMarca, VModelo, VAnio, ChasisMarca, ChasisAnio, TipoCombustible, VPotencia, NroEjes, VCaja, PocisionMotor, AnioFabricacion, Carroceria, Expediente, AireAco, Bar, Banio, Calefaccion, Suspension, Tara, PesoMax, CargaUtil, Asientos, idLocTMServ, TipoServTM, idTipoServicio, idClaseServicio, prestadorServ, CuitPrestServ, PTipoDoc, PNroDoc, PCuit, PDomicilio, PTelefono, PEmail, PidLocalidad, PTipoPersona, FechaHoraRep, TipoCarga, PCodigoPJ, CertificadoDiscapacidad, nroFactura, Activo, Status
           )
           VALUES 
           (
            :idPendiente, :Fecha, :Hora, :HoraFinal, :idTaller, :idFotovalidacion, :idVerificacion, :DominioVehiculo, :idHabilitacion, :codigoHabilitacion, :ChasisNro, :MotorAnio, :MotorMarca, :MotorNumero, :idLocalidadVehiculo, :TipoDocConductor, :NroDocConductor, :NombreConductor, :ApellidoConductor, :Reverificacion, :idVerificacionOriginal, :Inspector, :DirectorTecnico, :idEstado, :Eje1_Tara, :Eje2_Tara, :Eje3_Tara, :Eje4_Tara, :Eje1_FzaIzq, :Eje2_FzaIzq, :Eje3_FzaIzq, :Eje4_FzaIzq, :Eje1_FzaDer, :Eje2_FzaDer, :Eje3_FzaDer, :Eje4_FzaDer, :Eje1_Dif, :Eje2_Dif, :Eje3_Dif, :Eje4_Dif, :Eje1_Eficiencia, :Eje2_Eficiencia, :Eje3_Eficiencia, :Eje4_Eficiencia, :Eje5_Tara, :Eje5_FzaIzq, :Eje5_FzaDer, :Eje5_Dif, :Eje5_Eficiencia, :Alineacion, :NivelSonoro, :Interior, :Escape, :Bach, :PorcentajeCo, :Opac, :ppmHC, :Freno_FzaIsq, :Freno_FzaDer, :Freno_Dif, :Freno_Eficiencia, :CCCF, :MarcaTac, :NroTac, :RodadoTac, :NroInterno, :CodigoTitular, :DescripcionTitular, :Susp_FzaIsq, :Susp_FzaDer, :Susp_Dif, :Susp_Eficiencia, :Observaciones, :CompaniaSeguro, :NroPoliza, :UltimoRecPatente, :idTipoUso, :usuarioCarga, :idTipoVehiculo, :VMarca, :VModelo, :VAnio, :ChasisMarca, :ChasisAnio, :TipoCombustible, :VPotencia, :NroEjes, :VCaja, :PocisionMotor, :AnioFabricacion, :Carroceria, :Expediente, :AireAco, :Bar, :Banio, :Calefaccion, :Suspension, :Tara, :PesoMax, :CargaUtil, :Asientos, :idLocTMServ, :TipoServTM, :idTipoServicio, :idClaseServicio, :prestadorServ, :CuitPrestServ, :PTipoDoc, :PNroDoc, :PCuit, :PDomicilio, :PTelefono, :PEmail, :PidLocalidad, :PTipoPersona, now(), :TipoCarga, :PCodigoPJ, :CertificadoDiscapacidad, :nroFactura, :Activo, :Status
           )";
      }
      else
      {
        $ComandoSQL =
          "UPDATE pendientes
           SET
              Fecha= :Fecha, Hora= :Hora, HoraFinal= :HoraFinal, idFotovalidacion= :idFotovalidacion, idVerificacion= :idVerificacion, DominioVehiculo= :DominioVehiculo, idHabilitacion= :idHabilitacion, codigoHabilitacion= :codigoHabilitacion, ChasisNro= :ChasisNro, MotorAnio= :MotorAnio, MotorMarca= :MotorMarca, MotorNumero= :MotorNumero, idLocalidadVehiculo= :idLocalidadVehiculo, TipoDocConductor= :TipoDocConductor, NroDocConductor= :NroDocConductor, NombreConductor= :NombreConductor, ApellidoConductor= :ApellidoConductor, Reverificacion= :Reverificacion, idVerificacionOriginal= :idVerificacionOriginal, Inspector= :Inspector, DirectorTecnico= :DirectorTecnico, idEstado= :idEstado, Eje1_Tara= :Eje1_Tara, Eje2_Tara= :Eje2_Tara, Eje3_Tara= :Eje3_Tara, Eje4_Tara= :Eje4_Tara, Eje1_FzaIzq= :Eje1_FzaIzq, Eje2_FzaIzq= :Eje2_FzaIzq, Eje3_FzaIzq= :Eje3_FzaIzq, Eje4_FzaIzq= :Eje4_FzaIzq, Eje1_FzaDer= :Eje1_FzaDer, Eje2_FzaDer= :Eje2_FzaDer, Eje3_FzaDer= :Eje3_FzaDer, Eje4_FzaDer= :Eje4_FzaDer, Eje1_Dif= :Eje1_Dif, Eje2_Dif= :Eje2_Dif, Eje3_Dif= :Eje3_Dif, Eje4_Dif= :Eje4_Dif, Eje1_Eficiencia= :Eje1_Eficiencia, Eje2_Eficiencia= :Eje2_Eficiencia, Eje3_Eficiencia= :Eje3_Eficiencia, Eje4_Eficiencia= :Eje4_Eficiencia, Eje5_Tara= :Eje5_Tara, Eje5_FzaIzq= :Eje5_FzaIzq, Eje5_FzaDer= :Eje5_FzaDer, Eje5_Dif= :Eje5_Dif, Eje5_Eficiencia= :Eje5_Eficiencia, Alineacion= :Alineacion, NivelSonoro= :NivelSonoro, Interior= :Interior, Escape= :Escape, Bach= :Bach, PorcentajeCo= :PorcentajeCo, Opac= :Opac, ppmHC= :ppmHC, Freno_FzaIsq= :Freno_FzaIsq, Freno_FzaDer= :Freno_FzaDer, Freno_Dif= :Freno_Dif, Freno_Eficiencia= :Freno_Eficiencia, CCCF= :CCCF, MarcaTac= :MarcaTac, NroTac= :NroTac, RodadoTac= :RodadoTac, NroInterno= :NroInterno, CodigoTitular= :CodigoTitular, DescripcionTitular= :DescripcionTitular, Susp_FzaIsq= :Susp_FzaIsq, Susp_FzaDer= :Susp_FzaDer, Susp_Dif= :Susp_Dif, Susp_Eficiencia= :Susp_Eficiencia, Observaciones= :Observaciones, CompaniaSeguro= :CompaniaSeguro, NroPoliza= :NroPoliza, UltimoRecPatente= :UltimoRecPatente, idTipoUso= :idTipoUso, usuarioCarga= :usuarioCarga, idTipoVehiculo= :idTipoVehiculo, VMarca= :VMarca, VModelo= :VModelo, VAnio= :VAnio, ChasisMarca= :ChasisMarca, ChasisAnio= :ChasisAnio, TipoCombustible= :TipoCombustible, VPotencia= :VPotencia, NroEjes= :NroEjes, VCaja= :VCaja, PocisionMotor= :PocisionMotor, AnioFabricacion= :AnioFabricacion, Carroceria= :Carroceria, Expediente= :Expediente, AireAco= :AireAco, Bar= :Bar, Banio= :Banio, Calefaccion= :Calefaccion, Suspension= :Suspension, Tara= :Tara, PesoMax= :PesoMax, CargaUtil= :CargaUtil, Asientos= :Asientos, idLocTMServ= :idLocTMServ, TipoServTM= :TipoServTM, idTipoServicio= :idTipoServicio, idClaseServicio= :idClaseServicio, prestadorServ= :prestadorServ, CuitPrestServ= :CuitPrestServ, PTipoDoc= :PTipoDoc, PNroDoc= :PNroDoc, PCuit= :PCuit, PDomicilio= :PDomicilio, PTelefono= :PTelefono, PEmail= :PEmail, PidLocalidad= :PidLocalidad, PTipoPersona= :PTipoPersona, FechaHoraRep= now(), TipoCarga= :TipoCarga, PCodigoPJ= :PCodigoPJ, CertificadoDiscapacidad= :CertificadoDiscapacidad, nroFactura= :nroFactura, Activo= :Activo, Status= :Status
            WHERE 
             idPendiente=:idPendiente AND
             idTaller = :idTaller";
      }

      $SSQL = $dbUNC->prepare($ComandoSQL);

      // Binds
      //<editor-fold desc="Binds">
      $SSQL->bindValue(':idPendiente', $RPendiente["idPendiente"]);
      $SSQL->bindValue(':Fecha', $RPendiente["Fecha"]);
      $SSQL->bindValue(':Hora', $RPendiente["Hora"]);
      $SSQL->bindValue(':HoraFinal', $RPendiente["HoraFinal"]);
      $SSQL->bindValue(':idTaller', $RPendiente["idTaller"]);
      $SSQL->bindValue(':idFotovalidacion', $RPendiente["idFotovalidacion"]);
      $SSQL->bindValue(':idVerificacion', $RPendiente["idVerificacion"]);
      $SSQL->bindValue(':DominioVehiculo', $RPendiente["DominioVehiculo"]);
      $SSQL->bindValue(':idHabilitacion', $RPendiente["idHabilitacion"]);
      $SSQL->bindValue(':codigoHabilitacion', $RPendiente["codigoHabilitacion"]);
      $SSQL->bindValue(':ChasisNro', $RPendiente["ChasisNro"]);
      $SSQL->bindValue(':MotorAnio', $RPendiente["MotorAnio"]);
      $SSQL->bindValue(':MotorMarca', $RPendiente["MotorMarca"]);
      $SSQL->bindValue(':MotorNumero', $RPendiente["MotorNumero"]);
      $SSQL->bindValue(':idLocalidadVehiculo', $RPendiente["idLocalidadVehiculo"]);
      $SSQL->bindValue(':TipoDocConductor', $RPendiente["TipoDocConductor"]);
      $SSQL->bindValue(':NroDocConductor', $RPendiente["NroDocConductor"]);
      $SSQL->bindValue(':NombreConductor', $RPendiente["NombreConductor"]);
      $SSQL->bindValue(':ApellidoConductor', $RPendiente["ApellidoConductor"]);
      $SSQL->bindValue(':Reverificacion', $RPendiente["Reverificacion"]);
      $SSQL->bindValue(':idVerificacionOriginal', $RPendiente["idVerificacionOriginal"]);
      $SSQL->bindValue(':Inspector', $RPendiente["Inspector"]);
      $SSQL->bindValue(':DirectorTecnico', $RPendiente["DirectorTecnico"]);
      $SSQL->bindValue(':idEstado', $RPendiente["idEstado"]);
      $SSQL->bindValue(':Eje1_Tara', $RPendiente["Eje1_Tara"]);
      $SSQL->bindValue(':Eje2_Tara', $RPendiente["Eje2_Tara"]);
      $SSQL->bindValue(':Eje3_Tara', $RPendiente["Eje3_Tara"]);
      $SSQL->bindValue(':Eje4_Tara', $RPendiente["Eje4_Tara"]);
      $SSQL->bindValue(':Eje1_FzaIzq', $RPendiente["Eje1_FzaIzq"]);
      $SSQL->bindValue(':Eje2_FzaIzq', $RPendiente["Eje2_FzaIzq"]);
      $SSQL->bindValue(':Eje3_FzaIzq', $RPendiente["Eje3_FzaIzq"]);
      $SSQL->bindValue(':Eje4_FzaIzq', $RPendiente["Eje4_FzaIzq"]);
      $SSQL->bindValue(':Eje1_FzaDer', $RPendiente["Eje1_FzaDer"]);
      $SSQL->bindValue(':Eje2_FzaDer', $RPendiente["Eje2_FzaDer"]);
      $SSQL->bindValue(':Eje3_FzaDer', $RPendiente["Eje3_FzaDer"]);
      $SSQL->bindValue(':Eje4_FzaDer', $RPendiente["Eje4_FzaDer"]);
      $SSQL->bindValue(':Eje1_Dif', $RPendiente["Eje1_Dif"]);
      $SSQL->bindValue(':Eje2_Dif', $RPendiente["Eje2_Dif"]);
      $SSQL->bindValue(':Eje3_Dif', $RPendiente["Eje3_Dif"]);
      $SSQL->bindValue(':Eje4_Dif', $RPendiente["Eje4_Dif"]);
      $SSQL->bindValue(':Eje1_Eficiencia', $RPendiente["Eje1_Eficiencia"]);
      $SSQL->bindValue(':Eje2_Eficiencia', $RPendiente["Eje2_Eficiencia"]);
      $SSQL->bindValue(':Eje3_Eficiencia', $RPendiente["Eje3_Eficiencia"]);
      $SSQL->bindValue(':Eje4_Eficiencia', $RPendiente["Eje4_Eficiencia"]);
      $SSQL->bindValue(':Eje5_Tara', $RPendiente["Eje5_Tara"]);
      $SSQL->bindValue(':Eje5_FzaIzq', $RPendiente["Eje5_FzaIzq"]);
      $SSQL->bindValue(':Eje5_FzaDer', $RPendiente["Eje5_FzaDer"]);
      $SSQL->bindValue(':Eje5_Dif', $RPendiente["Eje5_Dif"]);
      $SSQL->bindValue(':Eje5_Eficiencia', $RPendiente["Eje5_Eficiencia"]);
      $SSQL->bindValue(':Alineacion', $RPendiente["Alineacion"]);
      $SSQL->bindValue(':NivelSonoro', $RPendiente["NivelSonoro"]);
      $SSQL->bindValue(':Interior', $RPendiente["Interior"]);
      $SSQL->bindValue(':Escape', $RPendiente["Escape"]);
      $SSQL->bindValue(':Bach', $RPendiente["Bach"]);
      $SSQL->bindValue(':PorcentajeCo', $RPendiente["PorcentajeCo"]);
      $SSQL->bindValue(':Opac', $RPendiente["Opac"]);
      $SSQL->bindValue(':ppmHC', $RPendiente["ppmHC"]);
      $SSQL->bindValue(':Freno_FzaIsq', $RPendiente["Freno_FzaIsq"]);
      $SSQL->bindValue(':Freno_FzaDer', $RPendiente["Freno_FzaDer"]);
      $SSQL->bindValue(':Freno_Dif', $RPendiente["Freno_Dif"]);
      $SSQL->bindValue(':Freno_Eficiencia', $RPendiente["Freno_Eficiencia"]);
      $SSQL->bindValue(':CCCF', $RPendiente["CCCF"]);
      $SSQL->bindValue(':MarcaTac', $RPendiente["MarcaTac"]);
      $SSQL->bindValue(':NroTac', $RPendiente["NroTac"]);
      $SSQL->bindValue(':RodadoTac', $RPendiente["RodadoTac"]);
      $SSQL->bindValue(':NroInterno', $RPendiente["NroInterno"]);
      $SSQL->bindValue(':CodigoTitular', $RPendiente["CodigoTitular"]);
      $SSQL->bindValue(':DescripcionTitular', $RPendiente["DescripcionTitular"]);
      $SSQL->bindValue(':Susp_FzaIsq', $RPendiente["Susp_FzaIsq"]);
      $SSQL->bindValue(':Susp_FzaDer', $RPendiente["Susp_FzaDer"]);
      $SSQL->bindValue(':Susp_Dif', $RPendiente["Susp_Dif"]);
      $SSQL->bindValue(':Susp_Eficiencia', $RPendiente["Susp_Eficiencia"]);
      $SSQL->bindValue(':Observaciones', $RPendiente["Observaciones"]);
      $SSQL->bindValue(':CompaniaSeguro', $RPendiente["CompaniaSeguro"]);
      $SSQL->bindValue(':NroPoliza', $RPendiente["NroPoliza"]);
      $SSQL->bindValue(':UltimoRecPatente', $RPendiente["UltimoRecPatente"]);
      $SSQL->bindValue(':idTipoUso', $RPendiente["idTipoUso"]);
      $SSQL->bindValue(':usuarioCarga', $RPendiente["usuarioCarga"]);
      $SSQL->bindValue(':idTipoVehiculo', $RPendiente["idTipoVehiculo"]);
      $SSQL->bindValue(':VMarca', $RPendiente["VMarca"]);
      $SSQL->bindValue(':VModelo', $RPendiente["VModelo"]);
      $SSQL->bindValue(':VAnio', $RPendiente["VAnio"]);
      $SSQL->bindValue(':ChasisMarca', $RPendiente["ChasisMarca"]);
      $SSQL->bindValue(':ChasisAnio', $RPendiente["ChasisAnio"]);
      $SSQL->bindValue(':TipoCombustible', $RPendiente["TipoCombustible"]);
      $SSQL->bindValue(':VPotencia', $RPendiente["VPotencia"]);
      $SSQL->bindValue(':NroEjes', $RPendiente["NroEjes"]);
      $SSQL->bindValue(':VCaja', $RPendiente["VCaja"]);
      $SSQL->bindValue(':PocisionMotor', $RPendiente["PocisionMotor"]);
      $SSQL->bindValue(':AnioFabricacion', $RPendiente["AnioFabricacion"]);
      $SSQL->bindValue(':Carroceria', $RPendiente["Carroceria"]);
      $SSQL->bindValue(':Expediente', $RPendiente["Expediente"]);
      $SSQL->bindValue(':AireAco', $RPendiente["AireAco"]);
      $SSQL->bindValue(':Bar', $RPendiente["Bar"]);
      $SSQL->bindValue(':Banio', $RPendiente["Banio"]);
      $SSQL->bindValue(':Calefaccion', $RPendiente["Calefaccion"]);
      $SSQL->bindValue(':Suspension', $RPendiente["Suspension"]);
      $SSQL->bindValue(':Tara', $RPendiente["Tara"]);
      $SSQL->bindValue(':PesoMax', $RPendiente["PesoMax"]);
      $SSQL->bindValue(':CargaUtil', $RPendiente["CargaUtil"]);
      $SSQL->bindValue(':Asientos', $RPendiente["Asientos"]);
      $SSQL->bindValue(':idLocTMServ', $RPendiente["idLocTMServ"]);
      $SSQL->bindValue(':TipoServTM', $RPendiente["TipoServTM"]);
      $SSQL->bindValue(':idTipoServicio', $RPendiente["idTipoServicio"]);
      $SSQL->bindValue(':idClaseServicio', $RPendiente["idClaseServicio"]);
      $SSQL->bindValue(':prestadorServ', $RPendiente["prestadorServ"]);
      $SSQL->bindValue(':CuitPrestServ', $RPendiente["CuitPrestServ"]);
      $SSQL->bindValue(':PTipoDoc', $RPendiente["PTipoDoc"]);
      $SSQL->bindValue(':PNroDoc', $RPendiente["PNroDoc"]);
      $SSQL->bindValue(':PCuit', $RPendiente["PCuit"]);
      $SSQL->bindValue(':PDomicilio', $RPendiente["PDomicilio"]);
      $SSQL->bindValue(':PTelefono', $RPendiente["PTelefono"]);
      $SSQL->bindValue(':PEmail', $RPendiente["PEmail"]);
      $SSQL->bindValue(':PidLocalidad', $RPendiente["PidLocalidad"]);
      $SSQL->bindValue(':PTipoPersona', $RPendiente["PTipoPersona"]);
      $SSQL->bindValue(':TipoCarga', $RPendiente["TipoCarga"]);
      $SSQL->bindValue(':PCodigoPJ', $RPendiente["PCodigoPJ"]);
      $SSQL->bindValue(':CertificadoDiscapacidad', $RPendiente["CertificadoDiscapacidad"]);
      $SSQL->bindValue(':nroFactura', $RPendiente["nroFactura"]);
      $SSQL->bindValue(':Activo', $RPendiente["Activo"]);
      $SSQL->bindValue(':Status', $RPendiente["Status"]);
      //</editor-fold>


      try
      {
        $Res = $SSQL->execute();

        if (!$Res) {
          print_r($SSQL->errorInfo());
          exit();
        }
      }
      catch (Exception $e)
      {
        $continua = false;
        exit("ERROR");
        throw $e;
      }

      // Ahora a marcar como replicado
      $ComandoSQL=
        " UPDATE pendientes 
        SET
          Replicado= 1
          WHERE
            idTaller = :idTaller AND 
            idPendiente = :idPendiente";

      $SSQL = $base->prepare($ComandoSQL);

      $SSQL->bindValue(':idPendiente', $RPendiente["idPendiente"]);
      $SSQL->bindValue(':idTaller', $RPendiente["idTaller"]);

      $Res= $SSQL->execute();
      if (!$Res) {
        print_r($SSQL->errorInfo());
        exit();
      }
      else
        $cantPendientes++;



    }
  }
}

/***************************************************************************
 *
 *				PENDIENTESDEFECTOS
 *
 **************************************************************************/
if($continua)
{
  $sql = "SELECT * FROM pendientesdefectos WHERE Replicado = 0 AND idTaller = $idTaller";

  $respPendientesDefectos = $base->query($sql);

  if ($respPendientesDefectos)
  {
    /***** Por cada pendientedefecto vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respPendientesDefectos as $PD)
    {
      // Pregunto si ya están en central para hacer insert / update
      $ComandoSQL =
        " SELECT COUNT(*) as HM  
        FROM pendientesdefectos
        WHERE
          idTaller = :idTaller AND 
          idPendiente = :idPendiente AND
          idDefecto = :idDefecto";

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idPendiente', $PD["idPendiente"]);
      $SSQL->bindValue(':idTaller', $PD["idTaller"]);
      $SSQL->bindValue(':idDefecto', $PD["idDefecto"]);

      $Res = $SSQL->execute();
      $R = $SSQL->fetch();

      $HM = $R["HM"];

      if ($HM == 0)
      {
        $ComandoSQL =
          "INSERT INTO pendientesdefectos
          (
            idPendiente, idTaller, idDefecto, idNivel, FechaHoraRep, DescripcionRubro, DescripcionDefecto, Descripcion, Activo
          )
          VALUES
          (	
            :idPendiente, :idTaller, :idDefecto, :idNivel, NOW(), :DescripcionRubro, :DescripcionDefecto, :Descripcion, :Activo
          )";
      }
      else
      {
        $ComandoSQL=
          "UPDATE pendientesdefectos
            SET       
              idNivel= :idNivel,
              FechaHoraRep= NOW(),
              DescripcionRubro= :DescripcionRubro,
              DescripcionDefecto= :DescripcionDefecto,
              Descripcion= :Descripcion,
              Activo= :Activo
            WHERE 
                idPendiente= :idPendiente AND 
                idTaller= :idTaller AND 
                idDefecto= :idDefecto";
      }


      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idPendiente', $PD["idPendiente"]);
      $SSQL->bindValue(':idTaller', $PD["idTaller"]);
      $SSQL->bindValue(':idDefecto', $PD["idDefecto"]);
      $SSQL->bindValue(':idNivel', $PD["idNivel"]);
      $SSQL->bindValue(':DescripcionRubro', $PD["DescripcionRubro"]);
      $SSQL->bindValue(':DescripcionDefecto', $PD["DescripcionDefecto"]);
      $SSQL->bindValue(':Descripcion', $PD["Descripcion"]);
      $SSQL->bindValue(':Activo', $PD["Activo"]);

      try
      {
        $Res = $SSQL->execute();

        if (!$Res)
        {
          print_r($SSQL->errorInfo());
          exit();
        }

        // Marcar como replicado
        $sql =
          " UPDATE pendientesdefectos 
            SET Replicado = 1 
            WHERE 
              idPendiente = " . $PD["idPendiente"] . " AND
              idTaller = " . $PD["idTaller"] . " AND
              idDefecto = " . $PD["idDefecto"];

        if(!$base->query($sql)){
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un al marcar como replicado un pendienteDefecto en taller. - Taller: $nomTaller";
          $continua = false;
          exit;
        }
        else
          $cantPendientesDefectos++;

      } catch (Exception $e)
      {
        $continua = false;
        exit("ERROR");
        throw $e;
      }
    }
  }
  else
  {
    $msnlog = "Ocurrio un error al obtener los pendientesDefectos. $nomTaller";
    $continua = false;
    exit("ERROR");
  }
}


/***************************************************************************
 *
 *				PENDIENTESSERVICIOS
 *
 **************************************************************************/
if($continua)
{
  $sql = "SELECT * FROM pendientesdefectos WHERE Replicado = 0 AND idTaller = $idTaller";

  $respPendientesServicios = $base->query($sql);

  if ($respPendientesServicios)
  {
    /***** Por cada pendientedefecto vamos a ver si esta el server => update, si no esta => insert ****/
    foreach ($respPendientesServicios as $PS)
    {
      // Pregunto si ya están en central para hacer insert / update
      $ComandoSQL =
        " SELECT COUNT(*) as HM  
        FROM pendientesdefectos
        WHERE
          idTaller = :idTaller AND 
          idPendienteServicios = :idPendienteServicios";

      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idPendienteServicios', $PS["idPendienteServicios"]);
      $SSQL->bindValue(':idTaller', $PS["idTaller"]);

      $Res = $SSQL->execute();
      $R = $SSQL->fetch();

      $HM = $R["HM"];

      if ($HM == 0)
      {
        $ComandoSQL =
          "INSERT INTO pendientesdefectos
          (
         	  idPendienteServicios, idPendiente, idTaller, idServicio, fechaHoraRep, activo
          )
          VALUES
          (	
            :idPendienteServicios, :idPendiente, :idTaller, :idServicio, NOW(), :activo
          )";

      }
      else
      {
        $ComandoSQL=
          "UPDATE pendientesdefectos
            SET                      
              idPendiente= :idPendiente, 
              idServicio= :idServicio, 
              fechaHoraRep= NOW(), 
              activo= :activo
            WHERE 
                idPendienteServicios= :idPendienteServicios AND 
                idTaller= :idTaller";
      }


      $SSQL = $dbUNC->prepare($ComandoSQL);

      $SSQL->bindValue(':idPendienteServicios', $PS["idPendienteServicios"]);
      $SSQL->bindValue(':idPendiente', $PS["idPendiente"]);
      $SSQL->bindValue(':idTaller', $PS["idTaller"]);
      $SSQL->bindValue(':idServicio', $PS["idServicio"]);
      $SSQL->bindValue(':activo', $PS["activo"]);


      try
      {
        $Res = $SSQL->execute();

        if (!$Res)
        {
          print_r($SSQL->errorInfo());
          exit();
        }

        // Marcar como replicado
        $sql =
          " UPDATE pendientesdefectos 
            SET Replicado = 1 
            WHERE 
              idPendienteServicios = " . $PS["idPendienteServicios"] . " AND
              idTaller = " . $PS["idTaller"];

        if(!$base->query($sql)){
          /**** SI OCURRIO UN ERROR CORTAMOS EL PROCESO *****/
          $msnlog = "Ocurrio un al marcar como replicado un pendienteServicio en taller. - Taller: $nomTaller";
          $continua = false;
          exit;
        }
        else
          $cantPendientesServicios++;

      } catch (Exception $e)
      {
        $continua = false;
        exit("ERROR");
        throw $e;
      }
    }
  }
  else
  {
    $msnlog = "Ocurrio un error al obtener los pendientesDefectos. $nomTaller";
    $continua = false;
    exit("ERROR");
  }
}


