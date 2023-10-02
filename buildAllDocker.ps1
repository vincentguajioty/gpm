param([string]$version)

cd backend

$command = "docker image build . -t gpm-bo:"+$version
Invoke-Expression $command

cd ..
cd frontend

$command = "docker image build . -f DockerFile-UAT -t gpm-fo-uat:"+$version
Invoke-Expression $command
$command = "docker image build . -f DockerFile-PROD -t gpm-fo-prod:"+$version
Invoke-Expression $command

cd ..

$command = "docker save gpm-bo:"+$version+" -o gpm-bo-"+$version+".tar"
Invoke-Expression $command
$command = "docker save gpm-fo-uat:"+$version+" -o gpm-fo-uat-"+$version+".tar"
Invoke-Expression $command
$command = "docker save gpm-fo-prod:"+$version+" -o gpm-fo-prod-"+$version+".tar"
Invoke-Expression $command