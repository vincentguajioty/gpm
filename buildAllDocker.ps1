param([string]$version)

cd backend

$command = "docker image build . -t gpm-bo:"+$version+" -t gpm-bo:latest"
Invoke-Expression $command

cd ..
cd frontend

$command = "docker image build . -f Dockerfile -t gpm-fo:"+$version+" -t gpm-fo:latest"
Invoke-Expression $command

cd ..

$command = "docker save gpm-bo:"+$version+" -o gpm-bo-"+$version+".tar"
Invoke-Expression $command
$command = "docker save gpm-fo:"+$version+" -o gpm-fo-"+$version+".tar"
Invoke-Expression $command

$command = "docker save gpm-bo:latest -o gpm-bo-latest.tar"
Invoke-Expression $command
$command = "docker save gpm-fo:latest -o gpm-fo-latest.tar"
Invoke-Expression $command

$command = "docker rmi gpm-bo:"+$version
Invoke-Expression $command
$command = "docker rmi gpm-fo:"+$version
Invoke-Expression $command
$command = "docker rmi gpm-bo:latest"
Invoke-Expression $command
$command = "docker rmi gpm-fo:latest"
Invoke-Expression $command