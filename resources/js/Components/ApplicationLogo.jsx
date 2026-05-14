export default function ApplicationLogo(props) {
    return (
        <img src="/logo.png" {...props} alt="Logo Pabrik Tempe Pak Iwan" style={{ objectFit: 'cover', borderRadius: '4px' }} />
    );
}
